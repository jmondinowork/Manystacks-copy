<?php

return [
    "tenant" => [
        [
            "name" => "microsoft",
            "logo" => "/images/Microsoft_logo.svg",
            "title" => "Microsoft Entra ID",
            "type" => "tenant",
            "method" => "oauth",
            "description" => "La connexion à Entra ID permet d’importer simplement et en toute sécurité tous vos collaborateurs et de les tenir à jour."
        ],
        [
            "name" => "google",
            "logo" => "/images/google-logo.png",
            "title" => "Google Workspace",
            "type" => "tenant",
            "method" => "oauth",
            "description" => "La connexion à Google Workspace permet d’importer simplement et en toute sécurité tous vos collaborateurs et de les tenir à jour."
        ]
    ],
    "sirh" => [
        [
            "name" => "payfit",
            "logo" => "/images/payfit-logo.jpg",
            "title" => "Payfit",
            "type" => "sirh",
            "method" => "oauth",
            "description" => "La connexion à Payfit permet d’importer simplement et en toute sécurité tous vos collaborateurs et de les tenir à jour."
        ],
        [
            "name" => "lucca",
            "logo" => "/images/lucca-logo.webp",
            "title" => "Lucca",
            "type" => "sirh",
            "method" => "shit",
            "description" => "La connexion à Lucca permet d’importer simplement et en toute sécurité tous vos collaborateurs et de les tenir à jour."
        ]
    ],
];
