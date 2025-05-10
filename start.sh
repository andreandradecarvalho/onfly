#!/bin/bash

# O Grande Show de Inicialização do Docker!
echo "🎉 Senhoras e senhores, preparem-se para a magia! Vamos botar esses contêineres Docker pra rodar! 🚀"

# Invocando os poderes do Docker
echo "🪄 Lançando o feitiço supremo: docker-compose up -d..."
docker-compose up -d

# Conferindo se a magia deu certo
if [ $? -eq 0 ]; then
  echo "🎊 Arrasou! Os contêineres estão de pé e prontos pra brilhar! 🙌"
else
  echo "😱 Caramba, os duendes do Docker bagunçaram tudo! Dê uma espiada no docker-compose.yml ou quem sabe apazigue os deuses com um .env novinho. 🪦"
  exit 1
fi

# Dando um tempinho pra API acordar (ela é meio dorminhoca)
echo "⏳ Dando uns segundinhos pra API tomar um café e ficar pronta..."
sleep 5

# Verificando a saúde da API com um GET maroto
echo "🩺 Checando o pulso da API em http://127.0.0.1:8000/api/healthcheck..."
for i in {1..3}; do
  RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8000/api/healthcheck)
  if [ "$RESPONSE" -eq 200 ]; then
    BODY=$(curl -s http://127.0.0.1:8000/api/healthcheck)
    if echo "$BODY" | grep -qi "ok"; then
      echo "🎉 API tá de boa, mais saudável que suco detox! Resposta: $BODY 😎"
      break
    else
      echo "😕 API respondeu 200, mas não tá dizendo 'ok'. Resposta: $BODY. Vou tentar de novo..."
    fi
  else
    echo "😿 API tá de mal, respondeu com código $RESPONSE. Talvez o .env tá de birra ou o banco tá de folga!"
  fi
  if [ $i -lt 3 ]; then
    echo "⏳ Tentando de novo em 3 segundos..."
    sleep 3
  else
    echo "😱 Três strikes, API tá fora! Verifique os logs com 'docker logs laravel_api' e veja se o .env ou o PostgreSQL tão aprontando."
  fi
done

# Pausa pra criar suspense
sleep 2

# Revelando as URLs e credenciais com estilo
echo "🌟 Eis as URLs sagradas da sua aplicação e as credenciais, direto do caldeirão do localhost!"
echo "---------------------------------------------------"
echo "🖥️  API Laravel (o chefão do pedaço): http://127.0.0.1:8000"
#echo "📊  Painel Horizon (pras filas mais organizadas): http://127.0.0.1:8000/horizon"
echo "🎨  Frontend Vue.js (tá brilhando que nem purpurina): http://localhost:5173"
echo "🔑  Credenciais de acesso:"
echo "     Usuário: onfly@onfly.com.br"
echo "     Senha: 123456"
echo "---------------------------------------------------"
echo "💡 Dica de mestre: Se der zica, veja se o .env tá brincando de esconde-esconde ou se o SQLSTATE[08006] tá de volta. 🕵️‍♂️"
echo "🚀 Manda ver no código, você é o cara! 😎"