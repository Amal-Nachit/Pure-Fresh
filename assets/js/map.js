// Charger l'API Google Maps avec ta clé
function loadGoogleMaps(apiKey) {
  if (
    document.querySelector(
      `script[src^="https://maps.googleapis.com/maps/api/js"]`
    )
  ) {
    console.warn("Google Maps API is already loaded.");
    return;
  }

  const script = document.createElement("script");
  script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&callback=initMap`;
  script.async = true;
  script.defer = true;
  document.head.appendChild(script);
}

// Initialiser la carte
function initMap(coordinates) {
  const mapDiv = document.getElementById("map");
  if (!mapDiv) {
    console.error('Element with ID "map" not found.');
    return;
  }
  
  const map = new google.maps.Map(mapDiv, {
    zoom: 6,
    center: { lat: 46.603354, lng: 1.888334 },
  });

  // Ajouter un marqueur pour chaque coordonnée  
  coordinates.forEach((coordinate) => {
    const location = new google.maps.LatLng(coordinate.lat, coordinate.lng);

    const marker = new google.maps.Marker({
      position: location,
      map: map,
    });

    const infoWindow = new google.maps.InfoWindow({
      content: `<p>${coordinate.nom}</p>`,
    });

    marker.addListener("click", () => infoWindow.open(map, marker));
  });
}

// Charger l'API Google Maps avec la clé après le chargement du DOM
document.addEventListener("DOMContentLoaded", function () {
  const apiKey = "AIzaSyDB7guuh8CY_MJasUE7LC5BV4eBTXWaVco";
  loadGoogleMaps(apiKey);
  initMap(JSON.parse("{{ coordinates|json_encode()|raw }}"));
});


