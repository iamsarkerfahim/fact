<?php
declare(strict_types=1);

namespace App\Services;

use Symfony\Component\HttpKernel\KernelInterface;

class BaseService
{
    /** @var KernelInterface $appKernel */
    protected $appKernel;

    /**
     * @required
     */
    public function setAppKernel(KernelInterface $appKernel): void
    {
        $this->appKernel = $appKernel;
    }
}
