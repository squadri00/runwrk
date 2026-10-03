<x-layouts.superadmin title="Audit log">
    @include('admin._logs')
    <div class="mt-4">{{ $logs->links() }}</div>
</x-layouts.superadmin>
