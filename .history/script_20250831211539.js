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

// -------- ROUTINE --------
let routineData = JSON.parse(localStorage.getItem("routine")) || {};

function renderRoutine() {
  const container = document.getElementById("routineTable");
  let html = "<table><tr><th>Day</th><th>Time</th><th>Course</th><th>Room</th><th>Action</th></tr>";
  for (const day in routineData) {
    routineData[day].forEach((row, i) => {
      html += `<tr>
        ${i===0 ? `<td rowspan='${routineData[day].length}'>${day}</td>` : ""}
        <td>${row.time}</td>
        <td>${row.course}</td>
        <td>${row.room}</td>
        <td><button class="delete-btn" onclick="deleteRoutine('${day}', ${i})"><i class="fa-solid fa-trash"></i></button></td>
      </tr>`;
    });
  }
  html += "</table>";
  container.innerHTML = html;
}
function addRoutine() {
  const pin = document.getElementById("crPinRoutine").value;
  if (pin !== CR_PIN) return alert("Wrong PIN!");
  const day = document.getElementById("daySelect").value;
  const time = document.getElementById("timeInput").value;
  const course = document.getElementById("courseInput").value;
  const room = document.getElementById("roomInput").value;
  if (!time || !course || !room) return alert("Fill all fields!");
  if (!routineData[day]) routineData[day] = [];
  routineData[day].push({ time, course, room });
  localStorage.setItem("routine", JSON.stringify(routineData));
  renderRoutine();
  document.getElementById("timeInput").value = "";
  document.getElementById("courseInput").value = "";
  document.getElementById("roomInput").value = "";
}
function deleteRoutine(day, index) {
  if(!confirm("Are you sure to delete this class?")) return;
  routineData[day].splice(index,1);
  if(routineData[day].length===0) delete routineData[day];
  localStorage.setItem("routine", JSON.stringify(routineData));
  renderRoutine();
}
renderRoutine();
function downloadPDF() {
  const element = document.getElementById("routineTable");
  html2pdf().from(element).save("Routine.pdf");
}

// -------- NOTICES --------
let notices = JSON.parse(localStorage.getItem("notices")) || [];
function saveNotices() {
  localStorage.setItem("notices", JSON.stringify(notices));
}
function renderNotices() {
  const noticeList = document.getElementById("noticeList");
  noticeList.innerHTML = "";
  notices.forEach((n, index) => {
    const li = document.createElement("li");
    li.innerHTML = `
      <small>${n.date}</small>
      <span>${n.text}</span>
      <button class="delete-btn" onclick="deleteNotice(${index})">
        <i class="fa-solid fa-trash"></i>
      </button>
    `;
    noticeList.appendChild(li);
  });
}
function addNotice() {
  const pin = document.getElementById("crPin").value;
  const newNotice = document.getElementById("newNotice").value;
  if (pin === CR_PIN && newNotice) {
    const now = new Date();
    notices.unshift({ text: newNotice, date: now.toLocaleString() });
    saveNotices();
    renderNotices();
    document.getElementById("newNotice").value = "";
  } else {
    alert("Wrong PIN or empty notice!");
  }
}
function deleteNotice(index) {
  if(!confirm("Delete this notice?")) return;
  notices.splice(index, 1);
  saveNotices();
  renderNotices();
}
renderNotices();

// -------- CONTACT --------
let contacts = JSON.parse(localStorage.getItem("contacts")) || [
  { role: "Class Representative", name: "Naim", detail: "01833-515057" },
  { role: "Advisor", name: "Dr. XYZ", detail: "advisor@puc.ac.bd" }
];
function renderContacts() {
  const container = document.getElementById("contactInfo");
  container.innerHTML = "";
  contacts.forEach((c,index) => {
    const div = document.createElement("div");
    div.className = "card";
    div.innerHTML = `<h3>${c.role}</h3>
      <p><i class="fa-solid fa-user"></i> ${c.name}</p>
      <p><i class="fa-solid fa-phone"></i> ${c.detail}</p>
      <button class="delete-btn" onclick="deleteContact(${index})"><i class="fa-solid fa-trash"></i></button>
    `;
    container.appendChild(div);
  });
}
function addContact() {
  const pin = document.getElementById("crPinContact").value;
  if (pin !== CR_PIN) return alert("Wrong PIN!");
  const role = document.getElementById("contactRole").value;
  const name = document.getElementById("contactName").value;
  const detail = document.getElementById("contactDetail").value;
  if (!role || !name || !detail) return alert("Fill all fields!");
  contacts = contacts.filter(c => c.role !== role);
  contacts.push({ role, name, detail });
  localStorage.setItem("contacts", JSON.stringify(contacts));
  renderContacts();
}
function deleteContact(index){
  if(!confirm("Delete this contact?")) return;
  contacts.splice(index,1);
  localStorage.setItem("contacts", JSON.stringify(contacts));
  renderContacts();
}
renderContacts();
