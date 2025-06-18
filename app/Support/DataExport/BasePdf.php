<?php

namespace App\Support\DataExport;

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

        $this->setTextColorArray($this->header_text_color);

        $this->setFont(family: 'dejavusans', style: 'B', size: 12);
        $this->MultiCell(w: 0, h: 0, txt: $headerData['title']);
        $this->Cell(w: 0, txt: config('app.name'), ln: 1);

        $this->setFont(family: 'courier', size: 10);
        $this->Cell(w: 0, txt: "Generado en fecha: {$headerData['string']}", align: 'R');
        $this->setLineStyle([
            'width' => 0.85 / $this->k,
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => 0,
            'color' => $headerData['line_color']
        ]);
        $this->Cell(w: 0, border: 'T', ln: 1);

        $this->setCreator(PDF_CREATOR);
        $this->setAuthor(config('app.name'));

        if (!App::environment('production'))
        {
            $watermark = $this->orientation == 'P' ? 'no-valid-v.png' : 'no-valid-h.png';
            $width = $this->orientation == 'P' ? 210 : 297;
            $height = $this->orientation == 'P' ? 297 : 210;
            $file = resource_path("images/watermarks/$watermark");

            $this->Image(file: $file, w: $width, h: $height, type: 'PNG');
        }
    }
}
