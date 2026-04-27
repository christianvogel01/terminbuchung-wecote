const dateInput = document.getElementById("dateInput");
const slotsContainer = document.getElementById("slots");
const bookingFormSection = document.getElementById("bookingForm");
const patientForm = document.getElementById("patientForm");
const selectedInfo = document.getElementById("selectedInfo");
const message = document.getElementById("message");
const selectedDateLabel = document.getElementById("selectedDateLabel");
const cancelBtn = document.getElementById("cancelBtn");

const morningTimes = ["08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30"];
const afternoonTimes = ["14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30"];

let selectedDate = "";
let selectedTime = "";

dateInput.addEventListener("change", async () => {
  selectedDate = dateInput.value;
  selectedTime = "";
  bookingFormSection.classList.add("hidden");
  clearMessage();

  if (!selectedDate) {
    selectedDateLabel.textContent = "Bitte wählen Sie zuerst ein Datum aus.";
    slotsContainer.innerHTML = '<p class="hint">Noch kein Datum ausgewählt.</p>';
    return;
  }

  selectedDateLabel.textContent = formatDate(selectedDate);
  await loadSlots(selectedDate);
});

async function loadSlots(date) {
  slotsContainer.innerHTML = '<p class="hint">Termine werden geladen...</p>';

  try {
    const response = await fetch(`get_slots.php?date=${encodeURIComponent(date)}`);
    const data = await response.json();

    if (!data.success) {
      showMessage(data.message, "error");
      return;
    }

    renderSlots(data.bookedSlots);
  } catch (error) {
    showMessage("Die Termine konnten nicht geladen werden.", "error");
  }
}

function renderSlots(bookedSlots) {
  slotsContainer.innerHTML = "";
  slotsContainer.appendChild(createTimeBlock("Vormittag", morningTimes, bookedSlots));
  slotsContainer.appendChild(createTimeBlock("Nachmittag", afternoonTimes, bookedSlots));
}

function createTimeBlock(title, times, bookedSlots) {
  const block = document.createElement("div");
  block.className = "time-block";

  const heading = document.createElement("h3");
  heading.textContent = title;

  const grid = document.createElement("div");
  grid.className = "slots";

  times.forEach(time => {
    const button = document.createElement("button");
    button.type = "button";
    button.textContent = time;
    button.className = "slot";

    const isBooked = bookedSlots.some(booked => booked.startsWith(time));

    if (isBooked) {
      button.classList.add("booked");
      button.disabled = true;
      button.title = "Dieser Termin ist bereits belegt";
    } else {
      button.addEventListener("click", () => selectSlot(button, time));
    }

    grid.appendChild(button);
  });

  block.appendChild(heading);
  block.appendChild(grid);

  return block;
}

function selectSlot(button, time) {
  document.querySelectorAll(".slot").forEach(slot => slot.classList.remove("selected"));
  button.classList.add("selected");

  selectedTime = time;
  selectedInfo.textContent = `Ausgewählter Termin: ${formatDate(selectedDate)} um ${selectedTime} Uhr`;
  bookingFormSection.classList.remove("hidden");
  clearMessage();
  bookingFormSection.scrollIntoView({ behavior: "smooth", block: "start" });
}

patientForm.addEventListener("submit", async (event) => {
  event.preventDefault();

  if (!selectedDate || !selectedTime) {
    showMessage("Bitte wählen Sie zuerst Datum und Uhrzeit aus.", "error");
    return;
  }

  const formData = new FormData(patientForm);
  formData.append("date", selectedDate);
  formData.append("time", selectedTime);

  try {
    const response = await fetch("create_booking.php", {
      method: "POST",
      body: formData
    });

    const data = await response.json();

    if (data.success) {
      showMessage(`Termin bestätigt: ${formatDate(selectedDate)} um ${selectedTime} Uhr.`, "success");
      patientForm.reset();
      bookingFormSection.classList.add("hidden");
      await loadSlots(selectedDate);
    } else {
      showMessage(data.message, "error");
      await loadSlots(selectedDate);
    }
  } catch (error) {
    showMessage("Die Buchung konnte nicht gespeichert werden.", "error");
  }
});

cancelBtn.addEventListener("click", () => {
  selectedTime = "";
  document.querySelectorAll(".slot").forEach(slot => slot.classList.remove("selected"));
  patientForm.reset();
  bookingFormSection.classList.add("hidden");
  clearMessage();
});

function showMessage(text, type) {
  message.textContent = text;
  message.className = `message ${type}`;
}

function clearMessage() {
  message.textContent = "";
  message.className = "message";
}

function formatDate(dateString) {
  const date = new Date(dateString + "T00:00:00");
  return date.toLocaleDateString("de-CH", {
    weekday: "long",
    day: "2-digit",
    month: "long",
    year: "numeric"
  });
}
