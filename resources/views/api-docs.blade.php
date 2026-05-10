<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Documentation API LMS</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script>
        window.onload = () => {
            SwaggerUIBundle({
                url: "/api-docs/swagger.yaml",
                dom_id: '#swagger-ui',
                presets: [
                    SwaggerUIBundle.presets.apis
                ],
                layout: "BaseLayout"
            });
        }
    </script>
</body>
</html>