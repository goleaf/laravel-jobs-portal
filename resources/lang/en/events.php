<?php

return [
    'application' => [
        'status_update' => [
            'title' => 'Application Status Update',
            'message' => 'Your application for :job changed from :old to :new.',
        ],
        'status_messages' => [
            'pending' => 'Your application is under review',
            'reviewed' => 'Your application has been reviewed',
            'shortlisted' => 'Congratulations! You have been shortlisted',
            'interview_scheduled' => 'Interview has been scheduled',
            'interview_completed' => 'Interview completed',
            'rejected' => 'Application was not successful this time',
            'hired' => 'Congratulations! You have been hired',
            'withdrawn' => 'Application has been withdrawn',
        ],
        'withdrawn' => [
            'title' => 'Application Withdrawn',
            'message' => ':candidate withdrew their application for :job.',
        ],
    ],
];


