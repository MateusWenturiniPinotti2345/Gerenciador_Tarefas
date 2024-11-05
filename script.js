// Inicializando o ScrollReveal
ScrollReveal().reveal('.container', { 
    duration: 1000,
    origin: 'bottom',
    distance: '50px'
});

// Função para carregar tarefas
function carregarTarefas() {
    fetch('api.php')
        .then(response => {
            if (!response.ok) throw new Error("Erro ao carregar tarefas");
            return response.json();
        })
        .then(tarefas => {
            const tabela = document.getElementById('tabelaTarefas');
            tabela.innerHTML = '';
            tarefas.forEach(tarefa => {
                const tr = document.createElement('tr');
                tr.classList.add('reveal-item'); // Classe para animação com ScrollReveal
                tr.innerHTML = `
                    <td>${tarefa.id}</td>
                    <td>${tarefa.descricao}</td>
                    <td>${tarefa.concluida ? '<i class="fas fa-check text-success"></i>' : ''}</td>
                    <td><button class="btn btn-danger btn-sm" onclick="excluirTarefa(${tarefa.id})">Excluir</button></td>
                `;
                tabela.appendChild(tr);
            });
            // Aplicando ScrollReveal nos itens adicionados
            ScrollReveal().reveal('.reveal-item', { duration: 800, origin: 'left', distance: '20px' });
        })
        .catch(error => console.error("Erro:", error));
}

// Função para adicionar tarefa
function adicionarTarefa() {
    const novaTarefaInput = document.getElementById("novaTarefa");
    const tarefaTexto = novaTarefaInput.value.trim();

    if (tarefaTexto === "") {
        alert("Por favor, digite uma tarefa.");
        return;
    }

    fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ descricao: tarefaTexto })
    })
    .then(response => {
        if (!response.ok) throw new Error("Erro ao adicionar tarefa");
        return response.json();
    })
    .then(() => {
        carregarTarefas();
        novaTarefaInput.value = "";
    })
    .catch(error => console.error("Erro:", error));
}

// Função para excluir tarefa
function excluirTarefa(id) {
    fetch(`api.php?id=${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log(data); // Verifica a resposta do servidor no console
        if (data.status === "success") {
            carregarTarefas(); // Atualiza a tabela após exclusão bem-sucedida
        } else {
            console.error("Erro ao excluir tarefa:", data.message);
            alert("Erro ao excluir tarefa: " + data.message);
        }
    })
    .catch(error => console.error("Erro:", error));
}

// Carrega as tarefas ao carregar a página
document.addEventListener('DOMContentLoaded', carregarTarefas);
