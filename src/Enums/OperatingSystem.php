<?php

namespace Consignr\FilamentPrintNode\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;

enum OperatingSystem: string implements HasLabel, HasDescription, HasIcon
{
    case Windows = 'windows';
    case Osx = 'osx';
    case Bookworm = 'pi-bookworm-aarch64';
    case Ubuntu22 = 'ubuntu-22.04-x86_64';
    case Ubuntu20 = 'ubuntu-20.04-64bit';
    case BullseyeAarch = 'pi-bullseye-aarch64';
    case BullseyeArm = 'pi-bullseye-armv7l';
    case Stretch = 'pi-stretch';
    case Buster = 'pi-buster';
    case Ubuntu16_18 = 'ubuntu-16.04-18.04-18.10-64bit';
    case Debian = 'debian10-64bit';
    case CentOs = 'centos7-64bit';
    case Ubuntu16 = 'ubuntu-16.04-32bit';
    case Ubuntu19 = 'ubuntu-19.04-19.10-64bit';
    case Jessie = 'pi-jessie';

    public function getLabel(): string
    {
        return match($this) {
            self::Windows => 'Windows',
            self::Osx => 'OSX',
            self::Bookworm => 'Bookworm',
            self::Ubuntu22 => 'Ubuntu 22.04',
            self::Ubuntu20 => 'Ubuntu 20.04',
            self::BullseyeAarch => 'Bullseye',
            self::BullseyeArm => 'Bullseye',
            self::Stretch => 'Stretch',
            self::Buster => 'Buster',
            self::Ubuntu16_18 => 'Ubuntu 16.04|18.04|18.10',
            self::Debian => 'Debian 10',
            self::CentOs => 'CentOS 7',
            self::Ubuntu16 => 'Ubuntu 16.04',
            self::Ubuntu19 => 'Ubuntu 19.04|19.10',
            self::Jessie => 'Jessie'
        };
    }

    public function getDescription(): ?string
    {
        return match($this) {
            self::Windows => null,
            self::Osx => null,
            self::Bookworm => null,
            self::Ubuntu22 => '22.04',
            self::Ubuntu20 => '20.04',
            self::BullseyeAarch => null,
            self::BullseyeArm => null,
            self::Stretch => null,
            self::Buster => null,
            self::Ubuntu16_18 => '16.04-18.04-18.10',
            self::Debian => '10',
            self::CentOs => '7',
            self::Ubuntu16 => '16.04',
            self::Ubuntu19 => '19.04-19.10',
            self::Jessie => null
        };
    }

    public function getIcon(): ?string
    {
        return match($this) {
            self::Windows => 'fab-windows',
            self::Osx => 'fab-apple',
            self::Bookworm => 'fab-raspberry-pi',
            self::Ubuntu22 => 'fab-ubuntu',
            self::Ubuntu20 => 'fab-ubuntu',
            self::BullseyeAarch => 'fab-raspberry-pi',
            self::BullseyeArm => 'fab-raspberry-pi',
            self::Stretch => 'fab-raspberry-pi',
            self::Buster => 'fab-raspberry-pi',
            self::Ubuntu16_18 => 'fab-ubuntu',
            self::Debian => 'fab-debian',
            self::CentOs => 'fab-centos',
            self::Ubuntu16 => 'fab-ubuntu',
            self::Ubuntu19 => 'fab-ubuntu',
            self::Jessie => 'fab-raspberry-pi'
        };
    }

    public function getTooltip(): ?string
    {
        return match($this) {
            self::Windows => null,
            self::Osx => null,
            self::Bookworm => null,
            self::Ubuntu22 => 'x86_64',
            self::Ubuntu20 => '64bit',
            self::BullseyeAarch => 'AArch',
            self::BullseyeArm => 'ARMv7',
            self::Stretch => null,
            self::Buster => null,
            self::Ubuntu16_18 => '64bit',
            self::Debian => '64bit',
            self::CentOs => '64bit',
            self::Ubuntu16 => '32bit',
            self::Ubuntu19 => '64bit',
            self::Jessie => null
        };
    }
}