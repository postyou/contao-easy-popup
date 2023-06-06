<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-easy-popup.
 *
 * (c) John Doe
 *
 * @license LGPL-3.0-or-later
 */

namespace Postyou\ContaoEasyPopup\Tests;

use Postyou\ContaoEasyPopup\ContaoEasyPopup;
use PHPUnit\Framework\TestCase;

class ContaoEasyPopupTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $bundle = new ContaoEasyPopup();

        $this->assertInstanceOf('Postyou\ContaoEasyPopup\ContaoEasyPopup', $bundle);
    }
}
