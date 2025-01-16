function sortTable(columnIndex, isNumeric = false) {
    const table = document.getElementById("projectsTable");
    const rows = Array.from(table.rows).slice(1);

    const sortedRows = rows.sort((a, b) => {
        const cellA = a.cells[columnIndex].innerText.trim();
        const cellB = b.cells[columnIndex].innerText.trim();

        if (isNumeric) {
            return parseFloat(cellA.replace(/[^0-9.-]+/g, "")) - parseFloat(cellB.replace(/[^0-9.-]+/g, ""));
        }else{ return cellA.localeCompare(cellB);}


    });

    const tbody = table.tBodies[0];
    sortedRows.forEach(row => tbody.appendChild(row));
}
