Bem-vindo ao Projeto Full-Stack Laravel + Vue.js! 🚀
Seja bem-vindo ao Projeto Full-Stack, uma aplicação turbinada com Laravel no backend, Vue.js no frontend, PostgreSQL como banco de dados, e Redis para gerenciar filas com o Laravel Horizon. Prepare-se para uma aventura de desenvolvimento onde filas são mais organizadas que a mesa de um concurseiro e a API é mais saudável que suco detox! 😎

Aviso: Cuidado com o arquivo .env! Ele adora brincar de esconde-esconde e pode trazer de volta o temido SQLSTATE[08006]. 🕵️‍♂️

📖 Sobre a Aplicação
Essa aplicação é um exemplo de arquitetura full-stack, com:

Backend: Uma API RESTful construída com Laravel, servindo endpoints como /api/healthcheck e gerenciando filas com o Horizon.
Frontend: Uma interface dinâmica em Vue.js, conectada à API via http://api:8000.
Banco de Dados: PostgreSQL, armazenando dados com robustez.
Filas: Redis, garantindo que as filas do Horizon rodem lisas como purpurina.

Ideal para aprender, prototipar, ou simplesmente se divertir enfrentando conflitos de porta e gremlins do Docker! 🪦
🛠️ Pré-requisitos
Antes de mergulhar, você vai precisar de:

Docker e Docker Compose: Para rodar os contêineres como um maestro. 🐳
Composer: Para instalar dependências PHP no Laravel.
Git: Porque clonar repositórios é vida.
Curl: Para testar a saúde da API (já vem na maioria dos sistemas Linux/macOS).
Um café forte ☕ pra lidar com qualquer address already in use.

No Linux/macOS, instale os pré-requisitos:
sudo apt-get update
sudo apt-get install docker.io docker-compose git curl
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

No Windows, use WSL2 ou instale o Docker Desktop, Git, e Composer.
🚀 Configuração Inicial

Clone o repositório:
git clone <URL_DO_REPOSITORIO>
cd <NOME_DO_PROJETO>


Remova qualquer .env rebelde:O volume ./api:/var/www/html pode sobrescrever o .env gerado. Evite surpresas:
rm ./api/.env


Instale dependências do Laravel:
docker run --rm -v $(pwd)/api:/app composer install


Instale o Laravel Horizon (se ainda não estiver no projeto):
docker run --rm -v $(pwd)/api:/app composer require laravel/horizon
docker exec -it laravel_api php artisan horizon:install


Verifique o .env.example:Certifique-se de que ./api/.env.example existe e contém:
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_KEY=
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
QUEUE_CONNECTION=redis
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=null

Se não existir, crie-o com o conteúdo acima.


🎬 Iniciando a Aplicação

Suba os contêineres com o script mágico:
chmod +x start.sh
./start.sh

O script inicia os serviços (laravel_api, vue_app, postgres, redis) e verifica a saúde da API em http://127.0.0.1:8000/api/healthcheck. Se tudo der certo, você verá as URLs de acesso! 🌟

URLs de Acesso:

🖥️ API Laravel: http://127.0.0.1:8000
📊 Painel Horizon (para gerenciar filas): http://127.0.0.1:8000/horizon
🎨 Frontend Vue.js: http://localhost:5173


Inicie o Horizon (opcional):Se precisar rodar o Horizon manualmente (o entrypoint.sh já o inicia em background):
chmod +x horizon.sh
./horizon.sh

O Horizon gerencia filas e pode ser monitorado no painel /horizon.


🛑 Parando a Aplicação
Para dar um descanso aos contêineres:
chmod +x stop.sh
./stop.sh

Isso para e remove os contêineres, deixando o localhost mais quieto que cidade pequena à meia-noite! 🌙
🐞 Depuração
Se algo der errado, aqui vão algumas dicas:

Erro address already in use:

Verifique se a porta 6379 (Redis), 5432 (PostgreSQL), 8000 (API), ou 5173 (Vue.js) está ocupada:sudo lsof -i :6379
sudo kill -9 <PID>


Ou remova o mapeamento de porta do docker-compose.yml (ex.: ports: - "6379:6379").


Erro SQLSTATE[08006] ou .env ausente:

Cheque se o .env está no contêiner:docker exec -it laravel_api cat /var/www/html/.env


Se estiver faltando, crie manualmente:docker exec -it laravel_api nano /var/www/html/.env

Adicione as configurações do .env.example, salve (Ctrl+O, Enter), saia (Ctrl+X), e ajuste permissões:docker exec -it laravel_api chown www-data:www-data /var/www/html/.env
docker exec -it laravel_api chmod 664 /var/www/html/.env




Horizon não inicia:

Confirme que o Redis está rodando:docker exec -it laravel_api redis-cli -h redis -p 6379 ping

Deve retornar PONG.
Verifique os logs do Horizon:docker exec -it laravel_api cat /var/www/html/horizon.log




Logs do contêiner:
docker logs laravel_api

Procure por mensagens sobre .env, Redis, ou erros do Horizon.


📝 Notas Finais

Redis: Configurado para comunicação interna (REDIS_HOST=redis). Se precisar acessar do host, adicione ports: - "6380:6379" ao serviço redis no docker-compose.yml.
Scripts: start.sh, stop.sh, e horizon.sh são seus melhores amigos. Eles têm um toque de humor pra tornar o desenvolvimento mais leve! 😄
Dica de ouro: Sempre remova o ./api/.env local antes de subir os contêineres para evitar gremlins do Docker.

Manda ver no código, você é o cara! 🚀
