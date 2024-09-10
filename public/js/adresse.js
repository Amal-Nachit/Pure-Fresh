document.querySelector("#adresse_input").addEventListener("input", function () {
  let query = this.value;

  fetch(`/search-address?q=${encodeURIComponent(query)}`)
    .then((response) => response.json())
    .then((data) => {
      let suggestions = data.features; // Les suggestions d'adresses renvoyées par l'API

      // Affichage des suggestions (par exemple, dans un élément de liste)
      let suggestionsBox = document.querySelector("#suggestions-box");
      suggestionsBox.innerHTML = "";

      suggestions.forEach(function (feature) {
        let suggestion = document.createElement("div");
        suggestion.textContent = feature.properties.label;
        suggestion.addEventListener("click", function () {
          document.querySelector("#adresse_input").value =
            feature.properties.label;
          suggestionsBox.innerHTML = ""; // Masquer les suggestions après sélection
        });
        suggestionsBox.appendChild(suggestion);
      });
    });
});
