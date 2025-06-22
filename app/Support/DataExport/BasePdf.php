<?php

namespace App\Support\DataExport;

use App\Models\Organization\Organization;
use Illuminate\Support\Facades\App;
use TCPDF;

class BasePdf extends TCPDF
{
    public function __construct(
        protected string $orientation = 'P',
        protected string $format = 'A4',
    ) {
        parent::__construct(
            orientation: $orientation,
            format: $format,
        );
    }

    public function Header()
    {
        $headerData = $this->getHeaderData();

        $imgFile = Organization::active()->first()?->logo_path ?? $headerData['logo'];

        $this->Image(
            file: storage_path("app/public/{$imgFile}"),
            w: $headerData['logo_width'] ?? 45,
            type: 'PNG'
        );

        if (!App::environment('production'))
        {
            // get the current page break margin
            $bMargin = $this->getBreakMargin();
            // get current auto-page-break mode
            $auto_page_break = $this->AutoPageBreak;
            // disable auto-page-break
            $this->SetAutoPageBreak(false, 0);

            $watermark = $this->orientation == 'P' ? 'no-valid-v.png' : 'no-valid-h.png';
            $width = $this->orientation == 'P' ? 216 : 279;
            $height = $this->orientation == 'P' ? 279 : 216;
            $file = resource_path("images/watermarks/$watermark");

            $this->Image(file: $file, w: $width, h: $height);

            // restore auto-page-break status
            $this->SetAutoPageBreak($auto_page_break, $bMargin);
            // set the starting point for the page content
            $this->setPageMark();
        }

        $this->setCreator(PDF_CREATOR);
        $this->setAuthor(config('app.name'));

        $this->setTextColorArray($this->header_text_color);

        $this->setFont(family: 'dejavusans', style: 'B', size: 12);
        $this->MultiCell(w: 0, h: 0, txt: $headerData['title'], align: 'L', x: 80);
        $this->setX(80);
        $this->Cell(w: 0, txt: config('app.name'), ln: 1);

        \TCPDF_FONTS::addTTFfont(fontfile: resource_path('fonts/iosevka-33.2.5/IosevkaFixedSS12-Regular.ttf'));

        $this->setFont(family: 'iosevkafixedss12', size: 10);
        $this->Cell(w: 0, txt: "Generado en fecha: {$headerData['string']}", align: 'R');
        $this->setLineStyle([
            'width' => 0.85 / $this->k,
            'cap' => 'round',
            'join' => 'round',
            'dash' => '2',
            'color' => $headerData['line_color']
        ]);
        $this->Ln(10);
    }
}
