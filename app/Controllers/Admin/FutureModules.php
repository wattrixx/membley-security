<?php

namespace App\Controllers\Admin;

class FutureModules extends AdminBaseController
{
    public function pages(): string
    {
        return $this->notImplemented('Page management');
    }

    public function applications(): string
    {
        return $this->notImplemented('Applications');
    }

    public function securityEnquiries(): string
    {
        return $this->notImplemented('Security Services enquiries');
    }

    private function notImplemented(string $module): string
    {
        $this->response->setStatusCode(501);

        return view('admin/not_implemented', [
            'pageTitle'   => $module . ' | Membley Administration',
            'moduleTitle' => $module,
        ]);
    }
}
