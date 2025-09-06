<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistemas UMP</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            height: 100%;
            font-family: Arial, sans-serif;
        }

        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover; /* Mantém a proporção */
            transform: scale(1.3); /* Aplica o "crop" por zoom */
            transform-origin: center center; /* Mantém o centro fixo */
            z-index: -1;
        }

        /* Conteúdo centralizado */
        .content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100%;
            background: rgba(0, 0, 0, 0.4); /* Sutil overlay para contraste */
            color: white;
            text-align: center;
        }

        .buttons {
            display: flex;
            flex-direction: column; /* Empilha os botões */
            align-items: center;    /* Centraliza no eixo horizontal */
            gap: 15px;              /* Espaçamento entre os botões */
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            width: 220px; /* Todos terão a mesma largura */
            text-align: center;
            background: rgba(31, 1, 1, 0.5);
            border: 2px solid white;
            color: white;
            padding: 15px 0; /* Centraliza o texto verticalmente */
            font-size: 18px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn:hover {
            background: white;
            color: black;
        }
    </style>
</head>
<body>
    <!-- Substitua o src pelo seu vídeo -->
    <video autoplay muted loop class="video-background">
        <source src="/videos/home.mp4" type="video/mp4">
        Seu navegador não suporta vídeos em HTML5.
    </video>

    <div class="content">
        <h1 style="margin-bottom: 20px;">Bem-vindo</h1>
        <div class="buttons">
            <a href="{{ route('dashboard.home') }}" class="btn">Plataforma UMP</a>
        </div>
        <div class="buttons">
            <a href="{{ route('estatistica') }}" class="btn">Painel de Estatística</a>
        </div>
        
        <div class="buttons">
            <a href="{{ route('digesto') }}" class="btn">Digesto</a>
        </div>
        
        <div class="buttons">
            <a href="https://sisvoto.ump.app.br" class="btn">SISVOTO</a>
        </div>
        
        <div class="buttons">
            <a href="https://sicom.ump.app.br" class="btn">SICOM</a>
        </div>
    </div>
</body>
</html>