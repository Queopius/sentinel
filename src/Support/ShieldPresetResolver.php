<?php

declare(strict_types=1);

namespace Queopius\Shield\Support;

class ShieldPresetResolver
{
    public function resolvedConfig(array $config): array
    {
        $presetName = (string) ($config['preset'] ?? '');
        if ($presetName === '') {
            return $config;
        }

        $presets = (array) ($config['presets'] ?? []);
        $preset = (array) ($presets[$presetName] ?? []);
        if ($preset === []) {
            return $config;
        }

        return array_replace_recursive($preset, $config);
    }
}
