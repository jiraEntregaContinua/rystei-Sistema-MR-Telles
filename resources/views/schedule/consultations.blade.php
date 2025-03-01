@extends('layouts.app')

@section('content')
<div class="container">
  <h2 class="text-center mb-4">Marcar Consulta</h2>

  <!-- Formulário para agendar consulta -->
  <div class="form-container">
    <form id="consultationForm" action="{{ route('consultations.store') }}" method="POST">
      @csrf
      <!-- Campos fixos para consultas -->
      <input type="hidden" name="title" value="Consulta">
      <input type="hidden" name="color" value="#000000">
      <input type="hidden" name="all_day" value="0">
      
      <div class="mb-3">
        <label for="date" class="form-label">Escolha uma data:</label>
        <!-- Esse campo não envia diretamente o start; o JS combinará a data com o horário -->
        <input type="text" id="date" class="form-control" placeholder="Selecione a data" required>
        <div class="error-message" id="dateError"></div>
      </div>
      
      <div class="mb-3">
        <label for="time" class="form-label">Escolha um horário:</label>
        <select id="time" class="form-control" required>
          <option value="">Carregando horários...</option>
          <option value="08:00">08:00</option>
          <option value="09:00">09:00</option>
          <option value="10:00">10:00</option>
          <option value="11:00">11:00</option>
          <option value="14:00">14:00</option>
          <option value="15:00">15:00</option>
          <option value="16:00">16:00</option>
          <option value="17:00">17:00</option>
        </select>
        <div class="error-message" id="timeError"></div>
      </div>
      
      <div class="mb-3">
        <label for="consultation_type" class="form-label">Tipo de Consulta:</label>
        <select id="consultation_type" name="description" class="form-control" required>
          <option value="">Selecione</option>
          <option value="Direito Civil">Direito Civil</option>
          <option value="Direito Previdenciário">Direito Previdenciário</option>
          <option value="Direito Família">Direito Família</option>
          <option value="Direito Trabalhista">Direito Trabalhista</option>
          <option value="Outros">Outros</option>
        </select>
        <div class="error-message" id="typeError"></div>
      </div>
      
      <button type="submit" class="btn btn-success w-100">Agendar Consulta</button>
    </form>
  </div>

  <!-- Tabela para exibir as consultas marcadas -->
  <div class="card p-4 shadow-sm">
    <h4 class="mb-3">Suas Consultas Marcadas</h4>
    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th>Data</th>
          <th>Horário</th>
          <th>Tipo de Consulta</th>
          <th>Ação</th>
        </tr>
      </thead>
      <tbody id="consultationsTableBody">
        <!-- Dados serão carregados via JavaScript -->
      </tbody>
    </table>
  </div>
</div>
@endsection

@section('scripts')
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
  const dateInput = document.getElementById('date');
  const timeSelect = document.getElementById('time');
  const form = document.getElementById('consultationForm');
  const consultationsTableBody = document.getElementById('consultationsTableBody');

  // Inicializa o Flatpickr para o campo de data, permitindo apenas dias úteis (segunda a sexta)
  flatpickr(dateInput, {
    dateFormat: "Y-m-d",
    minDate: "today",
    disable: [
      function(date) {
        return (date.getDay() === 0 || date.getDay() === 6);
      }
    ],
    onChange: function(selectedDates, dateStr) {
      if (selectedDates.length > 0) {
        loadAvailableTimes(dateStr);
        // Aqui você pode optar por carregar as consultas com base no dia selecionado ou
        // carregar todas as consultas do usuário. No exemplo abaixo, vamos carregar todas.
        loadConsultations();
      }
    }
  });

  // Define os horários de negócio disponíveis
  const businessHours = [
    '08:00', '09:00', '10:00', '11:00',
    '14:00', '15:00', '16:00', '17:00'
  ];

  // Função para carregar os horários disponíveis via AJAX
  async function loadAvailableTimes(date) {
    try {
        timeSelect.innerHTML = '<option value="">Carregando horários...</option>';
        const response = await fetch(`{{ route('consultations.available') }}?date=${date}`);
        if (!response.ok) throw new Error('Erro ao carregar horários');
        
        const data = await response.json();
        const occupiedTimes = data.booked || [];
        
        timeSelect.innerHTML = '<option value="">Selecione um horário</option>';
        
        // Lista fixa de horários comerciais (com zeros à esquerda)
        const businessHours = ['08:00', '09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'];
        
        businessHours.forEach(time => {
            const option = document.createElement('option');
            option.value = time;
            option.textContent = time;
            
            if (occupiedTimes.includes(time)) {
                option.textContent += ' - Indisponível';
                option.disabled = true;
                option.style.color = 'red';
            }
            
            timeSelect.appendChild(option);
        });
    } catch (error) {
        showError('Erro ao carregar horários disponíveis');
        console.error(error);
    }
}

  // Função para carregar todas as consultas do usuário (sem filtrar pela data selecionada)
  async function loadConsultations() {
    try {
      const response = await fetch(`{{ route('consultations.get') }}`);
      if (!response.ok) throw new Error('Erro ao carregar consultas');
      const consultations = await response.json();
      // Filtra apenas as consultas cujo título é "Consulta"
      const userConsultations = consultations.filter(cons => cons.title === "Consulta");
      
      consultationsTableBody.innerHTML = userConsultations.map(cons => {
        const datePart = cons.start.split(" ")[0];
        const timePart = cons.start.split(" ")[1].slice(0,5);
        return `
          <tr>
            <td>${datePart}</td>
            <td>${timePart}</td>
            <td>${cons.description}</td>
            <td>
              <button class="btn btn-sm btn-danger" onclick="deleteConsultation(${cons.id})">Excluir</button>
            </td>
          </tr>
        `;
      }).join("");
    } catch (error) {
      console.error("Erro ao carregar consultas:", error);
      showError("Erro ao carregar consultas");
    }
  }

  // Carrega as consultas assim que a página for carregada
  loadConsultations();

  // Submissão do formulário via AJAX para agendar consulta
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    clearErrors();

    const date = dateInput.value;
    const time = timeSelect.value;
    const type = document.getElementById('consultation_type').value;

    if (!date || !time || !type) {
      return showError("Preencha todos os campos obrigatórios");
    }

    // Monta a data/hora de início (formato: "YYYY-MM-DD HH:mm")
    const start = `${date} ${time}`;
    const startDate = new Date(start.replace(" ", "T"));
    const endDate = new Date(startDate.getTime() + 3600000);
    const endYear = endDate.getFullYear();
    const endMonth = ("0" + (endDate.getMonth() + 1)).slice(-2);
    const endDay = ("0" + endDate.getDate()).slice(-2);
    const endHour = ("0" + endDate.getHours()).slice(-2);
    const endMinute = ("0" + endDate.getMinutes()).slice(-2);
    const end = `${endYear}-${endMonth}-${endDay} ${endHour}:${endMinute}`;

    try {
      const response = await fetch("{{ route('consultations.store') }}", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
          title: "Consulta",
          start: start,
          end: end,
          description: type,
          color: "#000000",
          all_day: false
        })
      });
      const data = await response.json();
      if (!response.ok) {
        const errors = Object.values(data.errors || {}).flat();
        throw new Error(errors.join("\n"));
      }
      showSuccess("Consulta agendada com sucesso!");
      form.reset();
      // Recarrega os horários para a data selecionada
      loadAvailableTimes(date);
      // Recarrega todas as consultas do usuário
      loadConsultations();
    } catch (error) {
      showError(error.message);
      console.error("Erro ao agendar consulta:", error);
    }
  });

  // Função global para excluir uma consulta
  window.deleteConsultation = async function(id) {
    if (!confirm("Deseja realmente excluir esta consulta?")) return;
    
    try {
      let deleteUrl = `{{ route('consultations.delete', ['id' => '__ID__']) }}`;
      deleteUrl = deleteUrl.replace('__ID__', id);
      const response = await fetch(deleteUrl, {
        method: "DELETE",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        }
      });
      if (!response.ok) throw new Error("Erro ao excluir consulta");
      showSuccess("Consulta excluída com sucesso!");
      loadConsultations();
      if(dateInput.value) {
        loadAvailableTimes(dateInput.value);
      }
    } catch (error) {
      showError(error.message);
      console.error(error);
    }
  };

  function clearErrors() {
    document.querySelectorAll(".error-message").forEach(el => el.textContent = "");
  }

  function showError(message) {
    const alertDiv = document.createElement("div");
    alertDiv.className = "alert alert-danger alert-floating";
    alertDiv.innerHTML = `<button type="button" class="btn-close" data-bs-dismiss="alert"></button>${message}`;
    document.body.appendChild(alertDiv);
    setTimeout(() => alertDiv.remove(), 5000);
  }

  function showSuccess(message) {
    const alertDiv = document.createElement("div");
    alertDiv.className = "alert alert-success alert-floating";
    alertDiv.textContent = message;
    document.body.appendChild(alertDiv);
    setTimeout(() => alertDiv.remove(), 3000);
  }
});
</script>
@endsection
