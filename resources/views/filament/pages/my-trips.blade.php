<x-filament-panels::page>
    @if ($this->activePage === 'list')
        {{ $this->table }}
    @else
        {{ $this->productInfolist }}

        <div wire:key="leaflet-view-{{ $selectedTrip->id }}" x-data="{
            point: @js($selectedTrip->start_location),
            map: null,
            marker: null,
            initMap() {
                if (this.map) return
                const fallback = [7.0722, 125.6131]
                this.map = L.map(this.$refs.map).setView(fallback, 13)
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(this.map)
                if (this.point?.lat && this.point?.lng) {
                    this.marker = L.marker([this.point.lat, this.point.lng]).addTo(this.map)
                    this.map.setView([this.point.lat, this.point.lng], 15)
                }
            }
        }" x-init="initMap()" class="w-full">
            <div x-ref="map" class="h-96 w-full rounded-lg border" style="height:400px;z-index:0;" wire:ignore></div>
        </div>
    @endif
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</x-filament-panels::page>
