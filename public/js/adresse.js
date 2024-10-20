function loadGoogleMaps(apiKey) {
  if (
    document.querySelector(
      `script[src^="https://maps.googleapis.com/maps/api/js"]`
    )
  ) {
    return;
  }

  const script = document.createElement("script");
  script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places&callback=initAutocomplete`;
  script.async = true;
  script.defer = true;
  document.head.appendChild(script);
}

// Initialiser l'autocompletion
function initAutocomplete() {
  const inputCreate = document.getElementById("registration_form_adresse");
  const inputEdit = document.getElementById("pure_user_adresse");
  let input = "";
  if (inputCreate || inputEdit) {
    input = inputCreate || inputEdit;
  } else {
    return;
  }

  const autocomplete = new google.maps.places.Autocomplete(input, {
    types: ["address"],
    componentRestrictions: { country: "fr" },
  });

  autocomplete.addListener("place_changed", function () {
    const place = autocomplete.getPlace();
    if (!place.geometry) {
      return;
    }
  });
}

document.addEventListener("DOMContentLoaded", function () {
  const apiKey = "AIzaSyDB7guuh8CY_MJasUE7LC5BV4eBTXWaVco";
  loadGoogleMaps(apiKey);
});
