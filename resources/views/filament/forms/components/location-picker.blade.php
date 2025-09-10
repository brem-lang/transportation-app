<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @assets
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
        <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    @endassets

    <div wire:key="leaflet-single-{{ $getStatePath() }}" x-data="{
        point: @js($getState()),
        map: null,
        marker: null,
        async sync() { await $wire.set('{{ $getStatePath() }}', this.point) },
        setMarker(lat, lng) {
            if (this.marker) this.map.removeLayer(this.marker)
            this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map)
            this.point = { lat, lng }
            this.sync()
            this.marker.on('dragend', () => {
                const pos = this.marker.getLatLng()
                this.point = { lat: pos.lat, lng: pos.lng }
                this.sync()
            })
            this.marker.on('contextmenu', (e) => {
                e.originalEvent?.preventDefault()
                this.map.removeLayer(this.marker)
                this.marker = null
                this.point = null
                this.sync()
            })
        },
        initMap() {
            if (this.map) return
            const fallback = [7.0722, 125.6131]
            this.map = L.map(this.$refs.map).setView(fallback, 13)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(this.map)
            if (this.point?.lat && this.point?.lng) {
                this.setMarker(this.point.lat, this.point.lng)
                this.map.setView([this.point.lat, this.point.lng], 13)
            } else if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        const { latitude, longitude } = pos.coords
                        this.map.setView([latitude, longitude], 15)
                        this.setMarker(latitude, longitude)
                    },
                    () => this.setMarker(fallback[0], fallback[1]), { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
                )
            }
            this.map.on('click', e => this.setMarker(e.latlng.lat, e.latlng.lng))
            L.Control.geocoder({ defaultMarkGeocode: false })
                .on('markgeocode', ev => {
                    const c = ev.geocode.center
                    this.setMarker(c.lat, c.lng)
                    this.map.setView(c, 15)
                })
                .addTo(this.map)
        }
    }" x-init="initMap()" class="w-full">
        <div x-ref="map" class="h-96 w-full rounded-lg border" style="height:400px;z-index:0;" wire:ignore></div>
    </div>
</x-dynamic-component>
