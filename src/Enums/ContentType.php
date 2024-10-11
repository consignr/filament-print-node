<?php

namespace Consignr\FilamentPrintNode\Enums;

enum ContentType: string
{
    case PdfUri = 'pdf_uri';
    case PdfBase64 = 'pdf_base64';
    case RawUri = 'raw_uri';
    case RawBase64 = 'raw_base64';
}