<?php

namespace App\Livewire\Timetable;

use Livewire\Component;
use App\Models\Timetable;
use App\Models\TimetableSlot;
use App\Models\TimetableEntry;
use App\Models\Program;
use App\Models\AcademicSession;
use App\Models\Subject;
use App\Models\Room;
use App\Models\User;

class TimetableBuilder extends Component
{
    public $programs = [];
    public $sessions = [];
    public $subjects = [];
    public $rooms = [];
    public $teachers = [];
    public $timetableSlots = [];

    // Filter/Select Timetable Cohort
    public string $selectedProgramId = '';
    public string $selectedSessionId = '';
    public int $selectedSemester = 1;
    public $activeTimetable = null;

    // Active Timetable entries
    public $entries = [];

    // View modes: daily, weekly, monthly
    public string $viewMode = 'weekly';
    public int $selectedDay = 1;
    public int $selectedMonth = 6;

    // Creating new entry
    public bool $isFormOpen = false;
    public string $new_subject_id = '';
    public string $new_slot_id = '';
    public string $new_room_id = '';
    public string $new_teacher_id = '';
    public int $new_day_of_week = 1; // 1=Mon, 2=Tue ... 7=Sun
    public string $new_entry_type = 'REGULAR'; // REGULAR, LAB, MAKEUP

    // Conflict warnings
    public array $conflictWarnings = [];

    public function mount()
    {
        if (!auth()->user() || !auth()->user()->hasRole(['Super Admin', 'Tenant Admin', 'Trainer'])) {
            abort(403, 'Timetable builder is restricted.');
        }

        $this->programs = Program::where('status', 'ACTIVE')->get();
        $this->sessions = AcademicSession::orderBy('start_date', 'desc')->get();
        $this->subjects = Subject::where('status', 'ACTIVE')->get();
        $this->rooms = Room::all();
        $this->timetableSlots = TimetableSlot::orderBy('sort_order')->get();

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;
        if ($tenantId) {
            $this->teachers = User::where('tenant_id', $tenantId)
                ->whereHas('roles', function($q) {
                    $q->where('name', 'Trainer');
                })->get();
        }
    }

    public function loadTimetable()
    {
        $this->validate([
            'selectedProgramId' => 'required|uuid',
            'selectedSessionId' => 'required|uuid',
            'selectedSemester' => 'required|integer',
        ]);

        $tenantId = app()->bound('currentTenant') ? app('currentTenant')->id : null;

        $this->activeTimetable = Timetable::firstOrCreate([
            'tenant_id' => $tenantId,
            'program_id' => $this->selectedProgramId,
            'academic_session_id' => $this->selectedSessionId,
            'semester_no' => $this->selectedSemester,
        ], [
            'title' => 'Schedule for Sem ' . $this->selectedSemester,
            'status' => 'PUBLISHED',
        ]);

        $this->loadEntries();
    }

    public function loadEntries()
    {
        if ($this->activeTimetable) {
            $this->entries = TimetableEntry::with('slot', 'subject', 'room', 'teacher')
                ->where('timetable_id', $this->activeTimetable->id)
                ->get();
        }
        $this->detectConflicts();
    }

    public function saveEntry()
    {
        $this->validate([
            'new_subject_id' => 'required|uuid',
            'new_slot_id' => 'required|uuid',
            'new_room_id' => 'required|uuid',
            'new_teacher_id' => 'required|uuid',
            'new_day_of_week' => 'required|integer|min:1|max:7',
        ]);

        // Create timetable entry
        TimetableEntry::create([
            'timetable_id' => $this->activeTimetable->id,
            'slot_id' => $this->new_slot_id,
            'subject_id' => $this->new_subject_id,
            'room_id' => $this->new_room_id,
            'teacher_user_id' => $this->new_teacher_id,
            'day_of_week' => $this->new_day_of_week,
            'entry_type' => $this->new_entry_type,
        ]);

        $this->isFormOpen = false;
        $this->reset(['new_subject_id', 'new_slot_id', 'new_room_id', 'new_teacher_id']);
        $this->loadEntries();
    }

    public function deleteEntry(string $id)
    {
        TimetableEntry::destroy($id);
        $this->loadEntries();
    }

    // Conflict detection engine (checks for slot/room overlaps, teacher overlaps, student conflicts, and room capacity limits)
    public function detectConflicts()
    {
        $this->conflictWarnings = [];
        if (empty($this->entries) || !$this->activeTimetable) {
            return;
        }

        $allEntries = TimetableEntry::with('timetable', 'slot', 'subject', 'room', 'teacher')->get();

        // 1. Get student user IDs for current active timetable cohort
        $cohortStudentIds = \App\Models\StudentSessionEnrollment::where('program_id', $this->activeTimetable->program_id)
            ->where('academic_session_id', $this->activeTimetable->academic_session_id)
            ->where('current_semester', $this->activeTimetable->semester_no)
            ->pluck('student_user_id')
            ->toArray();

        $studentCount = count($cohortStudentIds);

        foreach ($this->entries as $entry) {
            // 2. Room capacity check
            if ($entry->room && $entry->room->capacity > 0) {
                if ($studentCount > $entry->room->capacity) {
                    $this->conflictWarnings[] = "Room capacity exceeded: Classroom " . $entry->room->name . " has capacity of " . $entry->room->capacity . " students, but current cohort has " . $studentCount . " students enrolled.";
                }
            }

            foreach ($allEntries as $other) {
                if ($entry->id === $other->id) continue;

                // Time slot & day matches
                if ($entry->slot_id === $other->slot_id && $entry->day_of_week === $other->day_of_week) {
                    // Room clash
                    if ($entry->room_id === $other->room_id) {
                        $this->conflictWarnings[] = "Room clash: Room " . ($entry->room->name ?? '') . " is booked for " . ($other->subject->name ?? '') . " on " . $this->getDayName($entry->day_of_week) . " at " . ($entry->slot->label ?? '') . ".";
                    }
                    // Teacher clash
                    if ($entry->teacher_user_id === $other->teacher_user_id) {
                        $this->conflictWarnings[] = "Teacher clash: Instructor " . ($entry->teacher->first_name ?? '') . " " . ($entry->teacher->last_name ?? '') . " is assigned to " . ($other->subject->name ?? '') . " elsewhere at the same time.";
                    }

                    // 3. Student overlap clash (if timetables are different but share students)
                    if ($entry->timetable_id !== $other->timetable_id) {
                        $otherTimetable = $other->timetable;
                        if ($otherTimetable && !empty($cohortStudentIds)) {
                            $otherStudentIds = \App\Models\StudentSessionEnrollment::where('program_id', $otherTimetable->program_id)
                                ->where('academic_session_id', $otherTimetable->academic_session_id)
                                ->where('current_semester', $otherTimetable->semester_no)
                                ->pluck('student_user_id')
                                ->toArray();

                            $intersect = array_intersect($cohortStudentIds, $otherStudentIds);
                            if (!empty($intersect)) {
                                $clashingStudents = \App\Models\User::whereIn('id', array_slice($intersect, 0, 3))
                                    ->get()
                                    ->map(fn($u) => $u->first_name . ' ' . $u->last_name)
                                    ->implode(', ');
                                if (count($intersect) > 3) {
                                    $clashingStudents .= ' and ' . (count($intersect) - 3) . ' others';
                                }

                                $this->conflictWarnings[] = "Student conflict: {$clashingStudents} are scheduled for both " . ($entry->subject->name ?? '') . " and " . ($other->subject->name ?? '') . " on " . $this->getDayName($entry->day_of_week) . " at " . ($entry->slot->label ?? '') . ".";
                            }
                        }
                    }
                }
            }
        }

        $this->conflictWarnings = array_unique($this->conflictWarnings);
    }

    public function getDayName(int $day): string
    {
        $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
        return $days[$day] ?? '';
    }

    public function render()
    {
        return view('livewire.timetable.timetable-builder')
            ->layout('layouts.app');
    }
}
