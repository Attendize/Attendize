<?php

namespace App\Exports;

use App\Models\QuestionAnswer;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;

class SurveyExport implements FromQuery, WithHeadings, WithEvents
{
    use Exportable;

    public function __construct(int $event_id)
    {
        $this->event_id = $event_id;
    }

    /**
     * @return \Illuminate\Support\Query
     */
    public function query()
    {
        $query = QuestionAnswer::query()->where('question_answers.event_id', $this->event_id);
        $query->select([
            'attendees.first_name',
            'attendees.last_name',
            'attendees.email',
            'questions.title',
            'question_answers.answer_text',
        ])
            ->join('attendees', 'attendees.event_id', '=', 'question_answers.event_id')
            ->join('questions', 'questions.id', '=', 'question_answers.question_id');

        return $query;
    }

    public function headings(): array
    {
        return [
            trans("Attendee.first_name"),
            trans("Attendee.last_name"),
            trans("Attendee.email"),
            trans("Question.question"),
            trans("Question.question_options"),
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                $event->writer->getProperties()->setCreator(config('attendize.app_name'));
                $event->writer->getProperties()->setCompany(config('attendize.app_name'));
            },
        ];
    }
}
