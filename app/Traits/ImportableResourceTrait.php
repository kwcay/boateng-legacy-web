<?php namespace App\Traits;


trait ImportableResourceTrait
{
    public static function import(array $data)
    {
        $results = [
            'total' => count($data),
            'imported' => 0,
            'skipped' => 0
        ];

        foreach ($data as $resource)
        {
            // Performance check.
            if (!$resource instanceof static) {
                $results['skipped']++;
                continue;
            }

            // Validate.
            $test = static::validate($resource->getArrayableAttributes());
            if ($test->fails()) {
                $results['skipped']++;
                continue;
            }

            // Import resource.
            $resource->save() ? $results['imported']++ : $results['skipped']++;
        }

        return $results;
    }
}
