const CR_PIN = "4242"; // CR PIN

// -------- CLOCKS --------
function updateDigitalClock() {
  const now = new Date();
  document.getElementById("digitalClock").textContent =
    now.toLocaleTimeString();
}
setInterval(updateDigitalClock, 1000);
updateDigitalClock();

function drawAnalogClock() {
  const canvas = document.getElementById("analogClock");
  if (!canvas) return;
  const ctx = canvas.getContext("2d");
  const radius = canvas.height / 2;
  ctx.translate(radius, radius);
  setInterval(() => {
    ctx.clearRect(-radius, -radius, canvas.width, canvas.height);
    drawFace(ctx, radius);
    drawTime(ctx, radius);
  }, 1000);
}
function drawFace(ctx, radius) {
  ctx.beginPath();
  ctx.arc(0, 0, radius, 0, 2 * Math.PI);
  ctx.fillStyle = "#fff";
  ctx.fill();
  ctx.stroke();
}
function drawTime(ctx, radius) {
  const now = new Date();
  let hour = now.getHours();
  let minute = now.getMinutes();
  let second = now.getSeconds();
  hour = hour % 12;
  hour = (hour * Math.PI) / 6 + (minute * Math.PI) / (6 * 60);
  drawHand(ctx, hour, radius * 0.5, 6);
  minute = (minute * Math.PI) / 30 + (second * Math.PI) / (30 * 60);
  drawHand(ctx, minute, radius * 0.8, 4);
  second = (second * Math.PI) / 30;
  drawHand(ctx, second, radius * 0.9, 2, "red");
}
function drawHand(ctx, pos, length, width, color = "black") {
  ctx.beginPath();
  ctx.lineWidth = width;
  ctx.lineCap = "round";
  ctx.moveTo(0, 0);
  ctx.rotate(pos);
  ctx.strokeStyle = color;
  ctx.lineTo(0, -length);
  ctx.stroke();
  ctx.rotate(-pos);
}
drawAnalogClock();

// -------- ROUTINE (AJAX) --------
let routineData = {};

function fetchRoutine() {
  fetch("routine.php?action=fetch")
    .then((res) => res.json())
    .then((data) => {
      routineData = {};
      data.forEach((r) => {
        if (!routineData[r.day]) routineData[r.day] = [];
        routineData[r.day].push({
          time: r.time,
          course: r.course,
          room: r.room,
          id: r.id,
        });
      });
      renderRoutine();
    });
}

function renderRoutine() {
  const container = document.getElementById("routineTable");
  let html =
    "<table><tr><th>Day</th><th>Time</th><th>Course</th><th>Room</th><th>Action</th></tr>";
  for (const day in routineData) {
    routineData[day].forEach((row, i) => {
      html += `<tr>
        ${i === 0 ? `<td rowspan='${routineData[day].length}'>${day}</td>` : ""}
        <td>${row.time}</td>
        <td>${row.course}</td>
        <td>${row.room}</td>
        <td><button class="delete-btn" onclick="deleteRoutine(${row.id})"><i class="fa-solid fa-trash"></i></button></td>
      </tr>`;
    });
  }
  html += "</table>";
  container.innerHTML = html;
}

function addRoutine() {
  const pin = document.getElementById("crPinRoutine").value;
  const day = document.getElementById("daySelect").value;
  const time = document.getElementById("timeInput").value;
  const course = document.getElementById("courseInput").value;
  const room = document.getElementById("roomInput").value;

  if (!time || !course || !room) return alert("Fill all fields!");

  fetch("routine.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `day=${day}&time=${time}&course=${course}&room=${room}&pin=${pin}`,
  })
    .then((res) => res.text())
    .then((res) => {
      alert(res);
      fetchRoutine();
    });

  document.getElementById("timeInput").value = "";
  document.getElementById("courseInput").value = "";
  document.getElementById("roomInput").value = "";
}

function deleteRoutine(id) {
  const pin = prompt("Enter CR PIN to delete class:");
  if (!pin) return;
  fetch(`routine.php?delete_id=${id}&pin=${pin}`)
    .then((res) => res.text())
    .then((res) => {
      alert(res);
      fetchRoutine();
    });
}

function downloadPDF() {
  const element = document.getElementById("routineTable");
  html2pdf().from(element).save("Routine.pdf");
}
fetchRoutine();

// -------- NOTICES (AJAX) --------
let notices = [];

function fetchNotices() {
  fetch("notice.php?action=fetch")
    .then((res) => res.json())
    .then((data) => {
      notices = data;
      renderNotices();
    });
}

function renderNotices() {
  const noticeList = document.getElementById("noticeList");
  noticeList.innerHTML = "";
  notices.forEach((n) => {
    const li = document.createElement("li");
    li.innerHTML = `
      <small>${new Date(n.created_at).toLocaleString()}</small>
      <span id="noticeText-${n.id}">${n.text}</span>
      <button class="edit-btn" onclick="editNotice(${n.id})">Edit</button>
      <button class="delete-btn" onclick="deleteNotice(${n.id})">
        <i class="fa-solid fa-trash"></i>
      </button>
    `;
    noticeList.appendChild(li);
  });
}

// Add new notice
function addNotice() {
  const pin = document.getElementById("crPin").value;
  const text = document.getElementById("newNotice").value;
  if (!text) return alert("Empty notice!");
  fetch("notice.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `text=${encodeURIComponent(text)}&pin=${pin}`,
  })
    .then((res) => res.text())
    .then((res) => {
      alert(res);
      document.getElementById("newNotice").value = "";
      fetchNotices();
    });
}

// Delete notice
function deleteNotice(id) {
  const pin = prompt("Enter CR PIN to delete notice:");
  if (!pin) return;
  fetch(`notice.php?delete_id=${id}&pin=${pin}`)
    .then((res) => res.text())
    .then((res) => {
      alert(res);
      fetchNotices();
    });
}

// Edit notice
function editNotice(id) {
  const pin = prompt("Enter CR PIN to edit notice:");
  if (!pin) return;
  if (pin !== "4242") { alert("Wrong PIN!"); return; }

  const currentText = document.getElementById(`noticeText-${id}`).textContent;
  const newText = prompt("Edit notice text:", currentText);
  if (!newText) return;

  fetch("notice.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `edit_id=${id}&text=${encodeURIComponent(newText)}&pin=${pin}`,
  })
    .then((res) => res.text())
    .then((res) => {
      alert(res);
      fetchNotices(); // Refresh notices after edit
    });
}

fetchNotices();

// -------- CONTACTS (AJAX) --------
let contacts = [];

function fetchContacts() {
  fetch("contacts.php?action=fetch")
    .then((res) => res.json())
    .then((data) => {
      contacts = data;
      renderContacts();
    });
}

function renderContacts() {
  const container = document.getElementById("contactInfo");
  container.innerHTML = "";
  contacts.forEach((c) => {
    const div = document.createElement("div");
    div.className = "card";
    div.innerHTML = `<h3>${c.role}</h3>
      <p><i class="fa-solid fa-user"></i> ${c.name}</p>
      <p><i class="fa-solid fa-phone"></i> ${c.detail}</p>
      <button class="delete-btn" onclick="deleteContact(${c.id})"><i class="fa-solid fa-trash"></i></button>
    `;
    container.appendChild(div);
  });
}

function addContact() {
  const pin = document.getElementById("crPinContact").value;
  const role = document.getElementById("contactRole").value;
  const name = document.getElementById("contactName").value;
  const detail = document.getElementById("contactDetail").value;

  if (!role || !name || !detail) return alert("Fill all fields!");

  fetch("contacts.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `role=${encodeURIComponent(role)}&name=${encodeURIComponent(
      name
    )}&detail=${encodeURIComponent(detail)}&pin=${pin}`,
  })
    .then((res) => res.text())
    .then((res) => {
      alert(res);
      fetchContacts();
    });
}

function deleteContact(id) {
  const pin = prompt("Enter CR PIN to delete contact:");
  if (!pin) return;
  fetch(`contacts.php?delete_id=${id}&pin=${pin}`)
    .then((res) => res.text())
    .then((res) => {
      alert(res);
      fetchContacts();
    });
}
fetchContacts();
