// Charger l'API Google Maps avec ta clé
function loadGoogleMaps(apiKey) {
  const script = document.createElement("script");
  script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places&callback=initAutocomplete`;
  script.async = true;
  script.defer = true;
  document.head.appendChild(script);
}

// Initialiser l'autocompletion
function initAutocomplete() {
  const input = document.getElementById("registration_form_adresse");
  if (!input) {
    console.error('Element with ID "registration_form_adresse" not found.');
    return;
  }

  const autocomplete = new google.maps.places.Autocomplete(input, {
    types: ["address"],
    componentRestrictions: { country: "fr" },
  });

  autocomplete.addListener("place_changed", function () {
    const place = autocomplete.getPlace();
    if (!place.geometry) {
      console.log("No details available for input: '" + place.name + "'");
      return;
    }
  });
}

// Charger l'API Google Maps avec la clé après le chargement du DOM
document.addEventListener("DOMContentLoaded", function () {
  const apiKey = "AIzaSyDB7guuh8CY_MJasUE7LC5BV4eBTXWaVco"; 
  loadGoogleMaps(apiKey); 
});
