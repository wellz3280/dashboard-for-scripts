<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Script Dashboard</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    html, body {
      height: 100%;
    }
    body {
      font-family: Arial, sans-serif;
      background: #f5f7fa;
      padding: 0;
      font-size: 18px;
    }
    .dashboard {
      width: 100vw;
      height: 100vh;
      background: #fff;
      border: none;
      display: flex;
      flex-direction: column;
    }
    .header {
      background: #1f3251;
      color: white;
      padding: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 28px;
      font-weight: bold;
    }
    .status {
      font-size: 18px;
      display: flex;
      align-items: center;
      position: absolute;
      right: 20px;
    }
    .status-dot {
      height: 10px;
      width: 10px;
      background-color: #4cd964;
      margin-right: 8px;
    }
    .content {
      padding: 20px;
      flex: 1;
      overflow-y: auto;
    }
    .label {
      font-weight: bold;
      color: #5a5a5a;
    }
    .value {
      margin-bottom: 10px;
    }
    .progress-container {
      background: #e0e0e0;
      margin: 15px 0;
      height: 25px;
    }
    .progress-bar {
      height: 100%;
      background: linear-gradient(to right, red, orange, green);
      width: 65%;
    }
    .progress-info {
      display: flex;
      justify-content: space-between;
      font-size: 18px;
      margin-bottom: 15px;
    }
    .logs {
      background: #f0f0f0;
      padding: 15px;
      border: 1px solid #ccc;
      font-size: 18px;
    }
    .log {
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      min-height: 40px;
    }
    .log span {
      margin-right: 10px;
    }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
  </style>
</head>
<body>
  <div class="dashboard">
    <div class="header">
      <div><strong>SCRIPT DASHBOARD</strong></div>
      <div class="status">
        <div class="status-dot"></div>
        EM EXECUÇÃO
      </div>
    </div>
    <div class="content">
      <div class="value"><span class="label">Nome:</span> <span id="script-nome"></span></div>
      <div class="value"><span class="label">Descrição:</span> <span id="descricao"></span></div>
      <div class="value"><span class="label">Quantidade:</span> <span id="quantidade"></span></div>

      <div class="progress-container">
        <div class="progress-bar"></div>
      </div>
      <div class="progress-info">
        <div>65%</div>
        <div><strong>Efetuado:</strong> 64.350 arquivos</div>
      </div>

      <div class="label">Logs do Processo</div>
      <div class="logs">
        <div class="log"><span>14:21</span><span class="success">✔</span> Teste pra caraio</div>
      </div>
    </div>
  </div>

  <script>
    fetch('./config.json')
      .then(response => response.json())
      .then(data => {
        document.getElementById('script-nome').textContent = data.name;
        document.getElementById('descricao').textContent = data.description;
        document.getElementById('quantidade').textContent = data.amount + ' k';
      })
      .catch(error => console.error('Erro ao carregar o JSON:', error));

      function carregarLogs() {
  fetch('./app.log')
    .then(response => {
      if (!response.ok) {
        throw new Error('Erro ao carregar o arquivo de logs');
      }
      return response.text(); // Lê o conteúdo como texto
    })
    .then(text => {
      const logs = text
        .trim()
        .split('\n') // Divide o texto em linhas
        .map(line => {
          try {
            return JSON.parse(line); // Converte cada linha em um objeto JSON
          } catch (e) {
            console.error('Erro ao analisar linha do log:', line, e);
            return null; // Ignora linhas inválidas
          }
        })
        .filter(log => log !== null); // Remove linhas inválidas

      const container = document.querySelector('.logs');
      container.innerHTML = ''; // Limpa os logs anteriores

      logs.reverse().forEach(log => {
        const logDiv = document.createElement('div');
        logDiv.classList.add('log');

        const hora = log.hora || '--:--';
        const status = log.status || 'info';
        const mensagem = log.mensagem || '';

        let icon = '';
        let classe = '';

        switch (status) {
          case 'success':
            icon = '✔'; classe = 'success'; break;
          case 'error':
            icon = '✖'; classe = 'error'; break;
          case 'warning':
            icon = '⚠'; classe = 'warning'; break;
          default:
            icon = 'ℹ'; classe = 'info';
        }

        logDiv.innerHTML = `<span>${hora}</span><span class="${classe}">${icon}</span> ${mensagem}`;
        container.appendChild(logDiv);
      });
    })
    .catch(err => {
      console.error('Erro ao carregar logs:', err);
      const container = document.querySelector('.logs');
      container.innerHTML = '<div class="log error">Erro ao carregar os logs</div>';
    });
}

setInterval(carregarLogs, 1);
carregarLogs();

  </script>
</body>
</html>
