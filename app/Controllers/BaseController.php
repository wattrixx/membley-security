<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\MediaModel;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @param CLIRequest|IncomingRequest $request
     *
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    /**
     * @param list<array<string, mixed>> $records
     * @return list<array<string, mixed>>
     */
    protected function addImagePaths(array $records): array
    {
        foreach ($records as $index => $record) {
            $path = $this->publicImagePath($record['featured_image'] ?? null);
            $records[$index]['featured_image_path'] = $path;
            $records[$index]['featured_image_alt'] = $path === null
                ? ''
                : $this->publicImageAlt($path);
        }

        return $records;
    }

    protected function publicImagePath(?string $path): ?string
    {
        $path = ltrim(trim((string) $path), '/');

        if (
            $path === ''
            || (! str_starts_with($path, 'assets/images/') && ! str_starts_with($path, 'uploads/'))
        ) {
            return null;
        }

        foreach (explode('/', $path) as $segment) {
            if (
                $segment === ''
                || $segment === '.'
                || $segment === '..'
                || preg_match('/[^A-Za-z0-9._-]/', $segment) === 1
            ) {
                return null;
            }
        }

        if (! in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return null;
        }

        return is_file(FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $path)) ? $path : null;
    }

    protected function imageAlt(string $path): string
    {
        $record = (new MediaModel())
            ->select('alt_text')
            ->where('file_path', $path)
            ->first();

        return trim((string) ($record['alt_text'] ?? ''));
    }
}
