const editDateInput = document.getElementById("editDateInput");
const editTimeInput = document.getElementById("editTimeInput");
const editSlots = document.getElementById("editSlots");
const editSlotHint = document.getElementById("editSlotHint");
const editSelectedInfo = document.getElementById("editSelectedInfo");
const editDateLabel = document.getElementById("editDateLabel");

const editMorningTimes = ["08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30"];
const editAfternoonTimes = ["14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00", "17:30"];

if (editDateInput) {
  loadEditSlots();

  editDateInput.addEventListener("change", () => {
    editTimeInput.value = "";
    editSelectedInfo.textContent = "Bitte wählen Sie eine Uhrzeit aus.";
    loadEditSlots();
  });
}

async function loadEditSlots() {
  const date = editDateInput.value;

  if (!date) {
    editSlots.innerHTML = "";
    editSlotHint.textContent = "Bitte zuerst ein Datum auswählen.";
    editDateLabel.textContent = "";
    return;
  }

  editDateLabel.textContent = formatEditDate(date);
  editSlotHint.textContent = "Verfügbare Zeiten werden geladen...";
  editSlots.innerHTML = "";

  try {
    const response = await fetch(
      `get_slots.php?date=${encodeURIComponent(date)}&exclude_id=${encodeURIComponent(window.currentBookingId || "")}`
    );

    const data = await response.json();

    if (!data.success) {
      editSlotHint.textContent = "Die verfügbaren Zeiten konnten nicht geladen werden.";
      return;
    }

    renderEditSlots(data.bookedSlots);
  } catch (error) {
    editSlotHint.textContent = "Die verfügbaren Zeiten konnten nicht geladen werden.";
  }
}

function renderEditSlots(bookedSlots) {
  editSlotHint.textContent = "";
  editSlots.innerHTML = "";

  editSlots.appendChild(createEditTimeBlock("Vormittag", editMorningTimes, bookedSlots));
  editSlots.appendChild(createEditTimeBlock("Nachmittag", editAfternoonTimes, bookedSlots));
}

function createEditTimeBlock(title, times, bookedSlots) {
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
    const isCurrent = editTimeInput.value === time;

    if (isCurrent) {
      button.classList.add("selected");
      editSelectedInfo.textContent = `Ausgewählte Uhrzeit: ${time} Uhr`;
    }

    if (isBooked) {
      button.classList.add("booked");
      button.disabled = true;
      button.title = "Dieser Termin ist bereits belegt";
    } else {
      button.addEventListener("click", () => {
        document.querySelectorAll("#editSlots .slot").forEach(slot => {
          slot.classList.remove("selected");
        });

        button.classList.add("selected");
        editTimeInput.value = time;
        editSelectedInfo.textContent = `Ausgewählte Uhrzeit: ${time} Uhr`;
      });
    }

    grid.appendChild(button);
  });

  block.appendChild(heading);
  block.appendChild(grid);

  return block;
}

function formatEditDate(dateString) {
  const date = new Date(dateString + "T00:00:00");
  return date.toLocaleDateString("de-CH", {
    weekday: "long",
    day: "2-digit",
    month: "long",
    year: "numeric"
  });
}
