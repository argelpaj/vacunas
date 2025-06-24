<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avance de Cobertura - Jeringas</title>
    <!-- Incluye Tailwind CSS CDN para estilos -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Estilos personalizados para Tailwind que no se pueden definir directamente en clases */
        .font-inter {
            font-family: 'Inter', sans-serif;
        }
        /* Definición de colores Tailwind personalizados */
        .text-\[0\.6rem\] { font-size: 0.6rem; } /* Approx 9.6px */
        .text-\[0\.55rem\] { font-size: 0.55rem; } /* Approx 8.8px */
        .w-\[1px\] { width: 1px; } /* Custom width for needle */
        .w-\[1\.5px\] { width: 1.5px; } /* Custom width for plunger rod */
        .border-\[1\.5px\] { border-width: 1.5px; } /* Custom border for needle bevel */
        .border-\[2px\] { border-width: 2px; } /* Custom border for barrel */
        .border-b-3 { border-bottom-width: 3px; } /* Custom border for needle bevel */
    </style>
</head>
<body class="min-h-screen bg-gray-100 flex flex-col items-center justify-start p-4 font-inter">

    <!-- ZONA SUPERIOR: Selector de Cantidad de Jeringas -->
    <div class="w-full bg-white p-4 rounded-lg shadow-xl mb-4 flex justify-center items-center">
        <div class="flex flex-col items-center w-40 text-center">
            <label for="num-syringes-input" class="block text-gray-700 text-sm font-medium mb-1">
                Cantidad:
            </label>
            <input
                type="number"
                id="num-syringes-input"
                class="w-full p-1 border-2 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-transparent text-center text-base"
                value="1"
                min="1"
                max="20"
                step="1"
            />
        </div>
    </div>

    <!-- ZONA INFERIOR: Título editable y Contenido de Jeringas -->
    <div class="w-full bg-white p-8 rounded-lg shadow-xl max-w-5xl text-center flex flex-col items-center justify-center">
        <!-- Título "Avance de cobertura" - ahora editable -->
        <h1 id="app-title-display" class="text-3xl md:text-4xl font-bold text-green-700 mb-6 cursor-pointer" style="display: block;">
            Avance de cobertura
        </h1>
        <input type="text" id="app-title-input" class="text-3xl md:text-4xl font-bold text-green-700 mb-6 text-center bg-gray-50 border-b-2 border-green-500 focus:outline-none" style="display: none;">

        <!-- Contenedor para múltiples jeringas - compartimento unificado -->
        <div id="syringes-container" class="flex flex-wrap justify-center gap-x-1 gap-y-6 w-full p-4 border border-gray-300 rounded-lg bg-gray-50 shadow-inner">
            <!-- Las jeringas se renderizarán aquí con JavaScript -->
        </div>
    </div>

    <script>
        // === Lógica JavaScript para la interactividad de las jeringas ===

        // Variables de estado global
        let numSyringes = 1;
        let syringeData = [{ id: 1, name: 'Vacunación 1', percentage: 86.32 }];
        let isEditingPercentage = {};
        let appTitle = 'Avance de cobertura'; // Estado para el título de la aplicación
        let isEditingTitle = false; // Estado para controlar la edición del título

        /**
         * Función para renderizar todas las jeringas en el DOM.
         * Esta función se llama cada vez que cambia el estado relevante.
         */
        function renderSyringes() {
            const syringesContainer = document.getElementById('syringes-container');
            syringesContainer.innerHTML = ''; // Limpiar el contenido existente para re-renderizar

            // --- Renderizar Título de la Aplicación ---
            const appTitleDisplay = document.getElementById('app-title-display');
            const appTitleInput = document.getElementById('app-title-input');

            if (isEditingTitle) {
                appTitleDisplay.style.display = 'none';
                appTitleInput.style.display = 'block';
                appTitleInput.value = appTitle;
                setTimeout(() => appTitleInput.focus(), 0);
            } else {
                appTitleDisplay.style.display = 'block';
                appTitleInput.style.display = 'none';
                appTitleDisplay.textContent = appTitle;
            }

            appTitleDisplay.onclick = () => {
                isEditingTitle = true;
                renderSyringes();
            };
            appTitleInput.onblur = () => {
                appTitle = appTitleInput.value;
                isEditingTitle = false;
                renderSyringes();
            };
            appTitleInput.onkeydown = (e) => {
                if (e.key === 'Enter') {
                    appTitle = appTitleInput.value;
                    isEditingTitle = false;
                    renderSyringes();
                }
            };

            syringeData.forEach((syringe, index) => {
                // Contenedor individual para cada jeringa (nombre, porcentaje, visual)
                const syringeWrapperDiv = document.createElement('div');
                syringeWrapperDiv.className = "flex flex-col items-center justify-start min-w-[70px] max-w-[90px] flex-grow-0 flex-shrink-0";

                // --- Elemento de Porcentaje (editable al hacer clic) ---
                const percentageDisplayDiv = document.createElement('div');
                percentageDisplayDiv.className = "flex flex-col items-center gap-0.5 mb-2";

                if (isEditingPercentage[index]) {
                    // Si se está editando, mostrar un input
                    const inputElement = document.createElement('input');
                    inputElement.type = 'number';
                    inputElement.value = syringe.percentage;
                    inputElement.className = "w-14 p-0.5 border-2 border-gray-300 rounded-md text-center text-[0.6rem] font-bold text-green-600 focus:outline-none focus:ring-1 focus:ring-green-500";
                    inputElement.onblur = () => {
                        // Al perder el foco, deshabilitar edición y re-renderizar
                        isEditingPercentage[index] = false;
                        renderSyringes();
                    };
                    inputElement.onkeydown = (e) => {
                        // Al presionar Enter, deshabilitar edición y re-renderizar
                        if (e.key === 'Enter') {
                            isEditingPercentage[index] = false;
                            renderSyringes();
                        }
                    };
                    inputElement.onchange = (e) => {
                        // Actualizar el valor del porcentaje en los datos
                        const newValue = parseFloat(e.target.value);
                        syringeData[index].percentage = Math.max(0, isNaN(newValue) ? 0 : newValue);
                    };
                    percentageDisplayDiv.appendChild(inputElement);
                    setTimeout(() => inputElement.focus(), 0); // Poner el foco en el input
                } else {
                    // Si no se está editando, mostrar el texto del porcentaje
                    const percentageElement = document.createElement('p');
                    percentageElement.className = "text-[0.6rem] font-bold text-green-600 cursor-pointer";
                    percentageElement.textContent = `${syringe.percentage.toFixed(2)}%`;
                    percentageElement.onclick = () => {
                        // Al hacer clic, habilitar edición y re-renderizar
                        isEditingPercentage[index] = true;
                        renderSyringes();
                    };
                    percentageDisplayDiv.appendChild(percentageElement);
                }
                syringeWrapperDiv.appendChild(percentageDisplayDiv);

                // --- Contenedor Visual de la Jeringa (partes de la jeringa) ---
                const syringeVisualsDiv = document.createElement('div');
                syringeVisualsDiv.className = "relative flex flex-col items-center mb-2";

                // Aguja y Capuchón
                const needleAssemblyDiv = document.createElement('div');
                needleAssemblyDiv.className = "relative flex flex-col items-center z-10";

                const needleDiv = document.createElement('div');
                needleDiv.className = "w-[1px] h-12 bg-gray-600 rounded-t-sm"; // Aguja
                needleAssemblyDiv.appendChild(needleDiv);

                const needleBevelDiv = document.createElement('div');
                needleBevelDiv.className = "absolute top-0 left-1/2 -translate-x-1/2 -mt-1 border-l-[1.5px] border-l-transparent border-r-[1.5px] border-r-transparent border-b-3 border-b-gray-600";
                needleAssemblyDiv.appendChild(needleBevelDiv);

                const orangeCapDiv = document.createElement('div');
                orangeCapDiv.className = "absolute bottom-[-0.25rem] w-4 h-5 bg-orange-500 rounded-t-sm rounded-b-sm";
                needleAssemblyDiv.appendChild(orangeCapDiv);

                const grayRingCapDiv = document.createElement('div');
                grayRingCapDiv.className = "absolute bottom-[-0.25rem] w-4 h-1 bg-gray-400 rounded-b-sm z-0";
                needleAssemblyDiv.appendChild(grayRingCapDiv);

                syringeVisualsDiv.appendChild(needleAssemblyDiv);

                // Barril de la Jeringa (parte principal)
                const barrelDiv = document.createElement('div');
                barrelDiv.className = "relative w-6 h-56 border-[2px] border-gray-400 rounded-b-lg rounded-t-sm overflow-hidden flex flex-col justify-end shadow-md -mt-1";

                const liquidDiv = document.createElement('div');
                liquidDiv.className = "absolute bottom-0 left-0 w-full bg-green-500 transition-all duration-500 ease-out z-10"; // Liquid z-index
                liquidDiv.style.height = `${Math.min(100, syringe.percentage)}%`; // Se llena hasta el 100% visualmente
                liquidDiv.style.borderBottomLeftRadius = '0.375rem';
                liquidDiv.style.borderBottomRightRadius = '0.375rem';
                barrelDiv.appendChild(liquidDiv);

                const outlineDiv = document.createElement('div');
                outlineDiv.className = "absolute inset-0 border-[2px] border-gray-400 rounded-b-lg rounded-t-sm pointer-events-none z-30";
                barrelDiv.appendChild(outlineDiv);

                // Marcas de la Jeringa
                const markingsDiv = document.createElement('div');
                markingsDiv.className = "absolute inset-y-0 right-0 w-2 flex flex-col justify-between py-2.5 px-0.5 z-20";
                for (let j = 0; j < 6; j++) {
                    const majorMark = document.createElement('div');
                    majorMark.className = "h-[1px] bg-gray-800 w-full";
                    markingsDiv.appendChild(majorMark);
                }
                const minorMarkingsContainer = document.createElement('div');
                minorMarkingsContainer.className = "absolute inset-y-0 right-0 w-1 flex flex-col justify-around py-2.5";
                for (let j = 0; j < 10; j++) {
                    const minorMark = document.createElement('div');
                    minorMark.className = "h-[0.5px] bg-gray-800 w-full opacity-70";
                    minorMarkingsContainer.appendChild(minorMark);
                }
                markingsDiv.appendChild(minorMarkingsContainer);
                barrelDiv.appendChild(markingsDiv);

                syringeVisualsDiv.appendChild(barrelDiv);

                // Émbolo (Varilla y Mango)
                const plungerRodDiv = document.createElement('div');
                plungerRodDiv.className = "w-[1.5px] h-8 bg-gray-400 relative -mt-1";
                syringeVisualsDiv.appendChild(plungerRodDiv);

                const plungerHandleDiv = document.createElement('div');
                plungerHandleDiv.className = "w-8 h-4 bg-white border border-gray-300 rounded-sm shadow-md -mt-1";
                syringeVisualsDiv.appendChild(plungerHandleDiv);

                syringeWrapperDiv.appendChild(syringeVisualsDiv);

                // --- Input para el Nombre (parte inferior) ---
                const nameInput = document.createElement('input');
                nameInput.type = 'text';
                nameInput.className = "w-full p-1 border-2 border-gray-300 rounded-md text-center text-[0.55rem] font-semibold text-gray-800";
                nameInput.value = syringe.name;
                nameInput.onchange = (e) => {
                    // Actualizar el nombre en los datos
                    syringeData[index].name = e.target.value;
                };
                nameInput.placeholder = `Nombre Jeringa ${syringe.id}`;
                syringeWrapperDiv.appendChild(nameInput);

                syringesContainer.appendChild(syringeWrapperDiv);
            });
        }

        // --- Manejadores de Eventos Globales ---

        // Manejar el cambio en el número de jeringas
        document.addEventListener('DOMContentLoaded', () => {
            const numSyringesInput = document.getElementById('num-syringes-input');
            numSyringesInput.onchange = (event) => {
                let value = parseInt(event.target.value);
                if (isNaN(value) || value < 1) {
                    value = 1;
                } else if (value > 20) {
                    value = 20;
                }
                numSyringes = value;

                const newSyringeData = Array(numSyringes).fill(null).map((_, index) => {
                    return syringeData[index] || { id: index + 1, name: `Vacunación ${index + 1}`, percentage: 0 };
                });
                syringeData = newSyringeData.slice(0, numSyringes);
                isEditingPercentage = {}; // Resetear el estado de edición al cambiar el número de jeringas
                renderSyringes();
            };

            // Renderizar las jeringas inicialmente cuando el DOM esté cargado
            renderSyringes();
        });
    </script>
</body>
</html>
