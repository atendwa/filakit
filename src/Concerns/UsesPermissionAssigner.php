<?php

namespace Atendwa\Filakit\Concerns;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Throwable;

trait UsesPermissionAssigner
{
    /**
     * @var Collection<(int|string), mixed>
     */
    protected Collection $permissions;

    protected Role $role;

    protected function role(string $name): Role
    {
        $this->reset();

        return $this->role = Role::firstOrCreate(['name' => $name], ['name' => $name]);
    }

    protected function persist(): void
    {
        $this->role->givePermissionTo($this->permissions->unique()->all());

        $this->reset();
    }

    /**
     * @param  class-string[]|class-string  $resources
     *
     * @throws Throwable
     */
    protected function all(array|string $resources): void
    {
        $resources = is_array($resources) ? $resources : [$resources];

        collect($resources)->each(function (string $resource) {
            $object = app($resource);

            if ($object instanceof HasShieldPermissions) {
                $this->custom($resource, $object::getPermissionPrefixes());

                return;
            }

            $this->specific($resource, 'review');
            $this->migrate($resource);
            $this->destroy($resource);
            $this->basic($resource);
        });
    }

    /**
     * @param  class-string[]|class-string  $resources
     *
     * @throws Throwable
     */
    protected function view(array|string $resources): void
    {
        $this->populate(is_array($resources) ? $resources : [$resources], ['view_any', 'view']);
    }

    /**
     * @param  class-string[]|class-string  $resources
     *
     * @throws Throwable
     */
    protected function migrate(array|string $resources): void
    {
        $this->populate(is_array($resources) ? $resources : [$resources], ['export', 'import']);
    }

    /**
     * @param  class-string[]|class-string  $resources
     *
     * @throws Throwable
     */
    protected function destroy(array|string $resources): void
    {
        $this->populate(
            is_array($resources) ? $resources : [$resources],
            ['restore', 'restore_any', 'delete', 'delete_any', 'force_delete', 'force_delete_any']
        );
    }

    /**
     * @param  class-string[]|class-string  $resources
     *
     * @throws Throwable
     */
    protected function specific(array|string $resources, string $permission): void
    {
        $this->populate(is_array($resources) ? $resources : [$resources], [$permission]);
    }

    /**
     * @param  class-string[]|class-string  $resources
     *
     * @throws Throwable
     */
    protected function basic(array|string $resources): void
    {
        $this->populate(is_array($resources) ? $resources : [$resources], ['view_any', 'view', 'create', 'update']);
    }

    /**
     * @param  class-string[]|class-string  $resources
     * @param  string[]  $prefixes
     *
     * @throws Throwable
     */
    protected function custom(array|string $resources, array $prefixes, bool $qualify = true): void
    {
        $this->populate(is_array($resources) ? $resources : [$resources], $prefixes, $qualify);
    }

    /**
     * @param  class-string[]|class-string  $pages
     *
     * @throws Throwable
     */
    protected function pages(array|string $pages): void
    {
        collect(is_array($pages) ? $pages : [$pages])->each(
            fn ($page) => $this->permissions->push($this->pagePermissionName($page))
        );
    }

    /**
     * @param  class-string[]|class-string  $widgets
     *
     * @throws Throwable
     */
    protected function widget(array|string $widgets): void
    {
        collect(is_array($widgets) ? $widgets : [$widgets])->each(
            fn ($widget) => $this->permissions->push(invade($widget)->method('getPermissionName'))
        );
    }

    protected function pagePermissionName(string $page): string
    {
        return Str::of(class_basename($page))
            ->prepend(Str::of(Utils::getPagePermissionPrefix())->append('_')->toString())
            ->toString();
    }

    /**
     * @param  class-string[]  $resources
     * @param  string[]  $prefixes
     *
     * @throws Throwable
     */
    protected function populate(array $resources, array $prefixes, bool $qualify = true): void
    {
        $prefixes = collect($prefixes);

        if (! $qualify) {
            collect($resources)->each(fn (string $resource) => $prefixes->each(
                fn (string $prefix) => $this->permissions->push($prefix)
            ));

            return;
        }

        collect($resources)->each(fn (string $resource) => $prefixes->each(
            fn (string $prefix) => $this->permissions->push(permission($prefix, $resource))
        ));
    }

    protected function reset(): void
    {
        $this->permissions = collect();
    }
}
