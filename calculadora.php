<?php
if (isset($_POST['facturacion'])) {
    $facturacion = floatval($_POST['facturacion']);
    $tramos = [
        4200 => 0.376,
        4400 => 0.382,
        4600 => 0.388,
        4800 => 0.393,
        5000 => 0.396,
        5200 => 0.401,
        5400 => 0.405,
        5600 => 0.409,
        5800 => 0.414,
        6000 => 0.418,
        6200 => 0.422,
        6400 => 0.426,
        6600 => 0.431,
        6800 => 0.435,
        7000 => 0.439,
        7200 => 0.444,
        7400 => 0.448,
        7600 => 0.452,
        7800 => 0.456,
        8000 => 0.461,
        8200 => 0.465,
        8400 => 0.469,
        8600 => 0.474,
        8800 => 0.478,
        9000 => 0.482,
        9200 => 0.487,
        9400 => 0.491,
        9600 => 0.495,
        9800 => 0.499,
        10000 => 0.504,
        10200 => 0.508,
        10400 => 0.512,
        10600 => 0.517,
        10800 => 0.521,
        11000 => 0.525,
        11457 => 0.530
    ];

    $sueldo_base = 1386.67;
    $incentivo = 0.0;
    $explicacion = "<ul>";
    $inicio_tramo = 4000;

    foreach ($tramos as $fin_tramo => $valor_euro) {
        if ($facturacion > $inicio_tramo) {
            $margen = min($facturacion, $fin_tramo) - $inicio_tramo;
            if ($margen > 0) {
                $monto = $margen * $valor_euro;
                $incentivo += $monto;
                $explicacion .= "<li>De ".number_format($inicio_tramo, 2, ',', '.')."€ a ".number_format(min($facturacion, $fin_tramo), 2, ',', '.')."€: ".
                    number_format($valor_euro * 100, 2, ',', '.')." cts/€ (media) × ".number_format($margen, 2, ',', '.')."€ = ".number_format($monto, 2, ',', '.')."€</li>";
            }
        }
        $inicio_tramo = $fin_tramo;
    }

    $sueldo_final = $sueldo_base + $incentivo;
    $explicacion .= "</ul>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calculadora de Incentivos</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --accent-color: #e74c3c;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
        }

        body {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: var(--light-gray);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        h1, h2, h3 {
            color: var(--dark-gray);
            margin-top: 0;
        }

        h1 {
            color: var(--primary-color);
            font-size: 2.2rem;
            margin-bottom: 1.5rem;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        label {
            font-weight: 600;
            color: var(--dark-gray);
        }

        input {
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border 0.3s;
        }

        input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: var(--secondary-color);
        }

        .btn-export {
            background-color: var(--accent-color);
        }

        .btn-export:hover {
            background-color: #c0392b;
        }

        .result-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }

        .result-value {
            font-weight: 600;
            color: var(--primary-color);
        }

        .tramo-item {
            padding: 0.5rem 0;
            border-bottom: 1px dashed #eee;
        }

        .disclaimer {
            font-size: 0.85rem;
            color: #666;
            font-style: italic;
            margin-top: 1.5rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            .card {
                padding: 1.5rem;
            }

            h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Calculadora de Incentivos</h1>
            <p>Introduce tu facturación bruta mensual para estimar tu salario bruto total incluyendo los incentivos.</p>
            <form method="post">
                <label for="facturacion">Bruto Gross (€):</label>
                <input type="number" step="0.01" min="0" name="facturacion" id="facturacion" required
                       value="<?= isset($facturacion) ? $facturacion : '' ?>" aria-label="Introduce tu facturación bruta mensual en euros">
                <button type="submit">Calcular</button>
            </form>
        </div>

        <?php if (isset($sueldo_final)): ?>
            <div class="card" id="resultado">
                <h2>Resultados aproximados</h2>

                <div class="result-item">
                    <span>Facturación introducida:</span>
                    <span class="result-value"><?= number_format($facturacion, 2, ',', '.') ?> €</span>
                </div>

                <div class="result-item">
                    <span>Sueldo base:</span>
                    <span class="result-value">1.381,34 €</span>
                </div>

                <div class="result-item">
                    <span>Incentivos calculados:</span>
                    <span class="result-value"><?= number_format($incentivo, 2, ',', '.') ?> €</span>
                </div>

                <div class="result-item" style="border-bottom: 2px solid var(--primary-color);">
                    <span><strong>Total sueldo bruto:</strong></span>
                    <span class="result-value" style="color: var(--accent-color); font-size: 1.1em;"><?= number_format($sueldo_final, 2, ',', '.') ?> €</span>
                </div>

                <h3 style="margin-top: 1.5rem;">Explicación de tramos aplicados</h3>
                <?= $explicacion ?>

                <p class="disclaimer">* Estas cifras son aproximadas y se basan en medias por tramo. Puede contener errores.</p>

                <button class="exportar btn-export" onclick="exportarPDF()">Exportar a PDF</button>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>¿Cómo funciona?</h2>
            <p>Tu sueldo fijo bruto es de 1.386,67 € al mes. A partir de 4.000 € de facturación, empiezas a ganar incentivos.</p>
            <p><strong>¿Cómo se calculan?</strong><br>
            Se divide la facturación en tramos de 200 € desde los 4.000 €. Por cada tramo, se paga una cantidad en céntimos por cada euro trabajado (por ejemplo, 0.376 significa que te dan 37,6 céntimos por cada euro que facturas en ese tramo).</p>
            <p><strong>Fórmula por tramo:</strong><br>
            <code>Incentivo tramo = (Euros facturados en el tramo) × (Céntimos por euro)</code></p>
            <p><strong>Fórmula total:</strong><br>
            <code>Total bruto = Sueldo base + Incentivos acumulados</code></p>
            <p>Cuanto más facturas, más sube el valor de cada euro en los siguientes tramos. Esta herramienta calcula eso automáticamente.</p>
        </div>
    </div>

    <script>
        function exportarPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait'
            });

            // Estilo del PDF
            doc.setFont('helvetica');
            doc.setTextColor(51, 51, 51);

            // Encabezado
            doc.setFontSize(18);
            doc.setTextColor(52, 152, 219);
            doc.text("Calculadora de Incentivos", 105, 20, { align: 'center' });

            doc.setFontSize(12);
            doc.setTextColor(51, 51, 51);
            doc.text("Resultado del cálculo", 105, 30, { align: 'center' });

            // Línea decorativa
            doc.setDrawColor(52, 152, 219);
            doc.setLineWidth(0.5);
            doc.line(20, 35, 190, 35);

            // Datos del cálculo
            doc.setFontSize(12);
            let y = 45;

            // Datos principales
            const datos = [
                { label: "Facturación introducida", value: "<?= number_format($facturacion, 2, ',', '.') ?> €" },
                { label: "Sueldo base", value: "1.386,67 €" },
                { label: "Incentivos calculados", value: "<?= number_format($incentivo, 2, ',', '.') ?> €" },
                { label: "Total sueldo bruto", value: "<?= number_format($sueldo_final, 2, ',', '.') ?> €" }
            ];

            datos.forEach(item => {
                doc.setFont('helvetica', 'bold');
                doc.text(item.label + ":", 20, y);
                doc.setFont('helvetica', 'normal');
                doc.text(item.value, 80, y);
                y += 8;
            });

            y += 5;

            // Tramos aplicados
            doc.setFont('helvetica', 'bold');
            doc.text("Tramos aplicados:", 20, y);
            y += 8;

            doc.setFont('helvetica', 'normal');
            doc.setFontSize(10);

            const tramosText = `<?= str_replace("\n", "", str_replace("</li>", "\n", str_replace("<li>", "", strip_tags($explicacion, "<li>")))) ?>`;
            const tramosLines = tramosText.split('\n');

            tramosLines.forEach(line => {
                if (line.trim() !== '') {
                    const lines = doc.splitTextToSize(line.trim(), 170);
                    lines.forEach(l => {
                        if (y > 270) {
                            doc.addPage();
                            y = 20;
                        }
                        doc.text(l, 20, y);
                        y += 5;
                    });
                    y += 3;
                }
            });

            // Pie de página
            doc.setFontSize(8);
            doc.setTextColor(102, 102, 102);
            doc.text("* Estas cifras son aproximadas y se basan en medias por tramo. Puede contener errores.", 105, 285, { align: 'center' });
            doc.text("Generado el " + new Date().toLocaleDateString(), 105, 290, { align: 'center' });

            doc.save("Calculadora_Incentivos_<?= isset($facturacion) ? number_format($facturacion, 0, '', '') : '' ?>.pdf");
        }

        // Focus automático al campo de facturación
        document.addEventListener('DOMContentLoaded', function() {
            const inputFacturacion = document.getElementById('facturacion');
            if (inputFacturacion) {
                inputFacturacion.focus();
            }

            // Validación en tiempo real
            if (inputFacturacion) {
                inputFacturacion.addEventListener('input', function(e) {
                    if (e.target.value < 0) {
                        e.target.setCustomValidity('La facturación no puede ser negativa');
                    } else {
                        e.target.setCustomValidity('');
                    }
                });
            }
        });
    </script>
</body>
</html>
