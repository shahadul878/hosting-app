<?php

namespace App\Enums;

enum ProductType: string
{
    case SharedHosting = 'shared_hosting';
    case WordPressHosting = 'wordpress_hosting';
    case Vps = 'vps';
    case Dedicated = 'dedicated';
    case DomainRegistration = 'domain_registration';
    case SslCertificate = 'ssl_certificate';
    case EmailHosting = 'email_hosting';
}
