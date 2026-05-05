

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tema 6 - Gestion de Equipos</title>
</head>
<body>
    <h1>Gestion de Equipos de Futbol</h1>
    <p>Tema 6: PHP + AJAX sobre la tabla equipos de la base de datos proyecto.</p>

    <h2>Agregar Equipo</h2>
    <form id="formAnadir" enctype="multipart/form-data">
        <div>
            <label for="nombre">Nombre del equipo:</label>
            <input id="nombre" name="nombre" type="text" required maxlength="120" placeholder="Ej: Real Madrid">
        </div>
        <div>
            <label for="fechafund">Año de fundación:</label>
            <input id="fechafund" name="fechafund" type="number" min="1800" max="2026" required placeholder="Ej: 1902">
        </div>
        <div>
            <label for="presupuesto">Presupuesto (€):</label>
            <input id="presupuesto" name="presupuesto" type="number" min="0" required placeholder="Ej: 50000000">
        </div>
        <div>
            <label for="logo">Logo (imagen):</label>
            <input id="logo" name="logo" type="file" accept="image/*">
        </div>
        <button type="submit">Agregar</button>
    </form>
    <p id="estado"></p>

    <h2>Lista de Equipos</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Logo</th>
                <th>Nombre</th>
                <th>Año Fund.</th>
                <th>Presupuesto (€)</th>
                <th>Puesto</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody id="tablaEquipos"></tbody>
    </table>

    <script>
        const API_URL = 'api_equipos.php';
        const tabla = document.getElementById('tablaEquipos');
        const form = document.getElementById('formAnadir');
        const estado = document.getElementById('estado');

        function setEstado(msg, ok = true) {
            estado.textContent = msg;
        }

        function buildPuestoSelect(id, actual, total) {
            const sel = document.createElement('select');
            for (let i = 1; i <= total; i++) {
                const op = document.createElement('option');
                op.value = String(i);
                op.textContent = String(i);
                if (i === actual) op.selected = true;
                sel.appendChild(op);
            }

            sel.addEventListener('change', async () => {
                const fd = new FormData();
                fd.append('accion', 'actualizar_puesto');
                fd.append('id', id);
                fd.append('puesto', sel.value);

                const res = await fetch(API_URL, { method: 'POST', body: fd });
                const data = await res.json();

                if (!data.ok) {
                    setEstado(data.error || 'Error al actualizar puesto.', false);
                    await cargarEquipos();
                    return;
                }

                setEstado('Puesto actualizado correctamente.');
                await cargarEquipos();
            });

            return sel;
        }

        function filaVacia() {
            const tr = document.createElement('tr');
            const td = document.createElement('td');
            td.colSpan = 7;
            td.textContent = 'No hay equipos registrados.';
            tr.appendChild(td);
            return tr;
        }

        async function cargarEquipos() {
            const res = await fetch(API_URL + '?accion=listar');
            const json = await res.json();

            tabla.innerHTML = '';

            if (!json.ok) {
                setEstado(json.error || 'No se pudo cargar la lista.', false);
                return;
            }

            const equipos = json.data.equipos;
            const total = json.data.total;

            if (!equipos.length) {
                tabla.appendChild(filaVacia());
                return;
            }

            for (const eq of equipos) {
                const tr = document.createElement('tr');

                const tdId = document.createElement('td');
                tdId.textContent = eq.id;
                tr.appendChild(tdId);

                const tdLogo = document.createElement('td');
                if (eq.logo_base64) {
                    const img = document.createElement('img');
                    img.alt = 'logo';
                    img.src = 'data:image/jpeg;base64,' + eq.logo_base64;
                    img.width = 60;
                    img.height = 60;
                    tdLogo.appendChild(img);
                } else {
                    tdLogo.textContent = '-';
                }
                tr.appendChild(tdLogo);

                const tdNom = document.createElement('td');
                tdNom.textContent = eq.nombre;
                tr.appendChild(tdNom);

                const tdFecha = document.createElement('td');
                tdFecha.textContent = new Date(eq.fechafund * 1000).getUTCFullYear();
                tr.appendChild(tdFecha);

                const tdPres = document.createElement('td');
                tdPres.textContent = eq.presupuesto.toLocaleString('es-ES');
                tr.appendChild(tdPres);

                const tdPuesto = document.createElement('td');
                tdPuesto.appendChild(buildPuestoSelect(eq.id, eq.puesto, total));
                tr.appendChild(tdPuesto);

                const tdDel = document.createElement('td');
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = 'Eliminar';
                btn.addEventListener('click', async () => {
                    if (!confirm('Quieres eliminar este equipo?')) return;

                    const fd = new FormData();
                    fd.append('accion', 'eliminar');
                    fd.append('id', eq.id);

                    const resDel = await fetch(API_URL, { method: 'POST', body: fd });
                    const dataDel = await resDel.json();

                    if (!dataDel.ok) {
                        setEstado(dataDel.error || 'Error al eliminar equipo.', false);
                        return;
                    }

                    setEstado('Equipo eliminado correctamente.');
                    await cargarEquipos();
                });
                tdDel.appendChild(btn);
                tr.appendChild(tdDel);

                tabla.appendChild(tr);
            }
        }

        form.addEventListener('submit', async (ev) => {
            ev.preventDefault();

            const fd = new FormData(form);
            fd.append('accion', 'anadir');

            const res = await fetch(API_URL, { method: 'POST', body: fd });
            const data = await res.json();

            if (!data.ok) {
                setEstado(data.error || 'No se pudo anadir el equipo.', false);
                return;
            }

            form.reset();
            setEstado('Equipo anadido correctamente.');
            await cargarEquipos();
        });

        cargarEquipos().catch(() => setEstado('Error al iniciar la aplicacion.', false));
    </script>
</body>
</html>
