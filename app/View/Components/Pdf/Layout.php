<?php

namespace App\View\Components\Pdf;

use App\Models\Organization\Organization;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Layout extends Component
{
    public string $logo = '';
    public string $title = 'REPORTE: GENÉRICO';
    public string $appName = '';
    public string $timestamp = '';

    /**
     * Create a new component instance.
     */
    public function __construct(?string $title)
    {
        $this->title = $title;
        $this->appName = config('app.name');
        $this->timestamp = now()->toDateTimeLocalString();
        $this->logo = $this->getLogo();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.pdf.layout');
    }

    private function getLogo(): string
    {
        $logo = resource_path('images/appLogo.jpg');
        $logoOrganization = Organization::active()->latest()->first()?->logo;

        if ($logoOrganization)
        {
            $logo = storage_path($logoOrganization);
        }

        return $logo;
    }
}
