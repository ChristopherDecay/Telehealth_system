<?php
// File overview: Handles footer functionality.
?>
<!-- Shared footer section and common client-side utilities. -->
<footer>
    <p>&copy; <?= date("Y") ?> MediCo TeleHealth System | Secure | Reliable | Connected Care</p>
    <div class="footer-contact" aria-label="Contact information">
        <span>Email: <a href="mailto:support@medico-telehealth.co.ke">support@medico-telehealth.co.ke</a></span>
        <span>Phone: <a href="tel:+254700000000">+254 700 000 000</a></span>
        
    </div>
    <script>
        (function () {
            // Attach a simple text filter UI above eligible tables.
            function addFilter(table) {
                if (!table || table.dataset.filterReady === "1") return;
                if (table.dataset.noFilter === "1") return;

                const wrapper = document.createElement("div");
                wrapper.className = "table-filter";

                const label = document.createElement("label");
                label.textContent = "Filter:";

                const input = document.createElement("input");
                input.type = "text";
                input.placeholder = "Type to search this table";

                wrapper.appendChild(label);
                wrapper.appendChild(input);

                table.parentNode.insertBefore(wrapper, table);
                table.dataset.filterReady = "1";

                // Filter body rows by matching entered text against row content.
                input.addEventListener("input", function () {
                    const term = input.value.trim().toLowerCase();
                    const rows = table.tBodies.length ? table.tBodies[0].rows : table.rows;
                    for (let i = 0; i < rows.length; i++) {
                        const row = rows[i];
                        if (row.parentNode.tagName !== "TBODY") continue;
                        const text = row.textContent.toLowerCase();
                        row.style.display = term === "" || text.indexOf(term) !== -1 ? "" : "none";
                    }
                });
            }

            // Initialize filters on all tables once DOM is ready.
            function initFilters() {
                const tables = document.querySelectorAll("table");
                tables.forEach(addFilter);
            }

            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", initFilters);
            } else {
                initFilters();
            }
        })();
    </script>
</footer>
</body>
</html>
