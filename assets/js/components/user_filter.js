// ==========================================================================
// ARCHIVO: FILTRADO POR TEXTO (POR EL NOMBRE O APELLIDO DEL USUARIO)
// Nota: por ahora solo funcional para las tablas de gestión de usuarios
// ==========================================================================

// Normaliza textos antes de comparar para las palabras con tildes
// Ejemplo: buscar "lopez", "López" o "LOPEZ" devuelve el mismo resultado
function normalize(str) {
  return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
}

document.addEventListener("DOMContentLoaded", () => {
  const filterInputs = document.querySelectorAll(".filter-input");

  filterInputs.forEach((input) => {
    const targetId = input.dataset.target;

    // Las columnas a filtrar se leen del atributo data-columns="0,1"
    // Si no se define, filtra todas las columnas
    const columns = input.dataset.columns
      ? input.dataset.columns.split(",").map(Number)
      : null;

    const table = document.getElementById(targetId);
    if (!table) return;

    const tbody = table.querySelector("tbody");

    const messageEl = input.dataset.message
      ? document.getElementById(input.dataset.message)
      : null;

    input.addEventListener("input", () => {
      const query = normalize(input.value.toLowerCase().trim());
      const rows = tbody.querySelectorAll("tr");

      let visibleCount = 0;

      rows.forEach((row) => {
        // Ignora la fila de estado vacío (empty-state)
        if (row.querySelector(".empty-state")) return;

        const cells = columns
          ? columns.map((i) => row.cells[i]).filter(Boolean)
          : Array.from(row.cells);

        const text = cells
          .map((c) => normalize(c.textContent.toLowerCase()))
          .join(" ");

        const matches = text.includes(query);
        row.style.display = matches ? "" : "none";

        if (matches) visibleCount++;
      });

      // Manejo del estado vacío cuando el filtro no arroja resultados
      let noResultsRow = tbody.querySelector(".filter-empty-state");

      if (visibleCount === 0 && query !== "") {
        table.closest(".table-container")?.classList.add("no-results");
        if (messageEl) messageEl.style.display = "none";

        if (!noResultsRow) {
          noResultsRow = document.createElement("tr");
          noResultsRow.className = "filter-empty-state";
          noResultsRow.innerHTML = `
            <td colspan="${table.rows[0]?.cells.length ?? 1}" class="empty-state">
              No se encontraron resultados para "<strong>${query}</strong>".
            </td>
          `;
          tbody.appendChild(noResultsRow);
        }
      } else {
        table.closest(".table-container")?.classList.remove("no-results");
        if (messageEl) messageEl.style.display = "";

        noResultsRow?.remove();
      }
    });
  });
});
