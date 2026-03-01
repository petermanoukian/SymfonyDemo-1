<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Filesystem\Filesystem;

class SystemController extends AbstractController
{
    public function clearCache(KernelInterface $kernel, Filesystem $filesystem): Response
    {
        $cacheDir = $kernel->getCacheDir();

        try {
            // This is the cleanest way to purge the system
            $filesystem->remove($cacheDir);
            
            return new Response('
                <html><body style="font-family:sans-serif; text-align:center; padding:50px;">
                    <h1 style="color:green;">✔ Cache Cleared!</h1>
                    <p>Old artifacts purged. The next page load will rebuild the system.</p>
                    <a href="/superadmin" style="color:blue; font-weight:bold;">← Back to Dashboard</a>
                </body></html>'
            );
        } catch (\Exception $e) {
            return new Response("Purge Failed: " . $e->getMessage(), 500);
        }
    }
}