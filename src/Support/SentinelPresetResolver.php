<?php

declare(strict_types=1);

namespace Queopius\Sentinel\Support;

class SentinelPresetResolver
{
    /**
     * @param  array<string,mixed>  $config
     * @return array<string,mixed>
     */
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
