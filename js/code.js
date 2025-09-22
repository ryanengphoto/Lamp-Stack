/*******************************
 * CONTACT MANAGER — SINGLE FILE FRONTEND (SPA)
 * - Renders Login, Create Account, and Contacts
 * - Uses location.hash routes: #login | #create | #contacts
 * - Uses existing LAMP endpoints
 *******************************/

/* ========= CONFIG ========= */
const API_ROOT = "http://COP4331-5.com/LAMPAPI";
const ENDPOINTS = {
  login:        `${API_ROOT}/Login.php`,
  registerUser: `${API_ROOT}/user`,               // .htaccess rewrites /user -> user.php
  addContact:   `${API_ROOT}/AddContact.php`,
  search:       `${API_ROOT}/SearchContacts.php`,
  delete:       `${API_ROOT}/DeleteContact.php`,  // expects { contactId } (optionally userId)
};
const COOKIE_TTL_MIN = 20;

let userId = 0;
let firstName = "";
let lastName  = "";

/* ========= ROUTER ========= */
window.addEventListener("hashchange", render);
document.addEventListener("DOMContentLoaded", () => {
  // default to login if no hash present
  if (!location.hash) location.hash = "#login";
  render();
});

function render() {
  const root = document.getElementById("app");
  if (!root) return;

  // Refresh cookie state on each render
  readCookie();

  const route = location.hash.replace("#", "");
  if (route === "create") {
    renderCreate(root);
  } else if (route === "contacts") {
    if (userId > 0) renderContacts(root);
    else location.hash = "#login";
  } else {
    renderLogin(root); // #login (default)
  }
}

/* ========= VIEW: LOGIN ========= */
function renderLogin(root) {
  root.innerHTML = `
    <h1 class="brand" style="margin-bottom:6px;">Welcome Back</h1>
    <p class="subtitle">Log in to continue</p>

    <form id="loginForm" class="auth-form">
      <label class="field">
        <span>Username</span>
        <input id="loginName" type="text" placeholder="Enter your username" required />
      </label>

      <label class="field">
        <span>Password</span>
        <input id="loginPassword" type="password" placeholder="••••••••" required />
      </label>

      <button type="submit" class="btn primary">LOGIN</button>
      <a class="btn ghost" id="toCreate" href="#create">Create Account</a>
    </form>

    <div id="loginResult" class="result"></div>
    <footer class="footer">
      <small>© <span id="year"></span> Contact Manager</small>
    </footer>
  `;

  const y = document.getElementById("year");
  if (y) y.textContent = new Date().getFullYear();

  document.getElementById("loginForm").addEventListener("submit", (e) => {
    e.preventDefault();
    doLogin();
  });
}

/* ========= VIEW: CREATE ACCOUNT ========= */
function renderCreate(root) {
  root.innerHTML = `
    <h1 class="brand" style="margin-bottom:6px;">Create your account</h1>
    <p class="subtitle">Join and start managing your contacts</p>

    <form id="createForm" class="auth-form">
      <label class="field">
        <span>First name</span>
        <input id="regFirstName" type="text" placeholder="Ada" required />
      </label>

      <label class="field">
        <span>Last name</span>
        <input id="regLastName" type="text" placeholder="Lovelace" required />
      </label>

      <label class="field">
        <span>Username</span>
        <input id="regLogin" type="text" placeholder="adal" required />
      </label>

      <label class="field">
        <span>Password</span>
        <input id="regPassword" type="password" placeholder="••••••••" required />
      </label>

      <button type="submit" class="btn primary">Create Account</button>
      <a class="btn ghost" href="#login">Back to Login</a>
    </form>

    <div id="createResult" class="result"></div>
    <footer class="footer">
      <small>© <span id="year"></span> Contact Manager</small>
    </footer>
  `;

  const y = document.getElementById("year");
  if (y) y.textContent = new Date().getFullYear();

  document.getElementById("createForm").addEventListener("submit", (e) => {
    e.preventDefault();
    doRegister();
  });
}

/* ========= VIEW: CONTACTS ========= */
function renderContacts(root) {
  root.innerHTML = `
    <header style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
      <h1 class="brand" style="margin:0;">Contacts</h1>
      <div>
        <span id="userGreeting" style="margin-right:10px;opacity:.8;"></span>
        <button class="btn ghost" id="logoutBtn">Logout</button>
      </div>
    </header>

    <!-- Search -->
    <form id="searchForm" class="auth-form" style="grid-template-columns:1fr auto;align-items:end;">
      <label class="field" style="grid-column:1/2;">
        <span>Search</span>
        <input id="searchText" type="text" placeholder="name, phone, or email"/>
      </label>
      <button class="btn primary" style="grid-column:2/3;">Search</button>
    </form>
    <div id="colorSearchResult" class="result"></div>

    <!-- Add contact -->
    <h2 style="margin:16px 0 8px;">Add Contact</h2>
    <form id="addForm" class="auth-form" style="grid-template-columns:repeat(4,1fr);">
      <label class="field">
        <span>First name</span>
        <input id="addFirstName" type="text" placeholder="Grace" required/>
      </label>
      <label class="field">
        <span>Last name</span>
        <input id="addLastName" type="text" placeholder="Hopper" required/>
      </label>
      <label class="field">
        <span>Phone</span>
        <input id="addPhone" type="text" placeholder="(555) 123-4567"/>
      </label>
      <label class="field">
        <span>Email</span>
        <input id="addEmail" type="email" placeholder="grace@example.com"/>
      </label>
      <button class="btn primary" style="grid-column:1/-1;">Add Contact</button>
    </form>
    <div id="colorAddResult" class="result"></div>

    <!-- Results table -->
    <div style="overflow-x:auto;margin-top:12px;">
      <table id="resultsTable" style="width:100%;border-collapse:collapse;">
        <thead>
          <tr style="text-align:left;">
            <th style="padding:8px;border-bottom:1px solid #ddd;">First</th>
            <th style="padding:8px;border-bottom:1px solid #ddd;">Last</th>
            <th style="padding:8px;border-bottom:1px solid #ddd;">Phone</th>
            <th style="padding:8px;border-bottom:1px solid #ddd;">Email</th>
            <th style="padding:8px;border-bottom:1px solid #ddd;">Actions</th>
          </tr>
        </thead>
        <tbody id="resultsBody"></tbody>
      </table>
    </div>
  `;

  // Greet + wire events
  const greet = document.getElementById("userGreeting");
  if (greet) greet.textContent = `Logged in as ${firstName} ${lastName}`;

  document.getElementById("logoutBtn").addEventListener("click", doLogout);
  document.getElementById("searchForm").addEventListener("submit", (e) => { e.preventDefault(); searchContacts(); });
  document.getElementById("addForm").addEventListener("submit", (e) => { e.preventDefault(); addContact(); });

  // Initial load
  searchContacts();
}

/* ========= AUTH ========= */
function doLogin() {
  userId = 0; firstName = ""; lastName = "";

  const login = (document.getElementById("loginName") || {}).value || "";
  const password = (document.getElementById("loginPassword") || {}).value || "";
  const out = document.getElementById("loginResult");
  if (out) out.textContent = "";

  if (!login || !password) { if (out) out.textContent = "Please enter username and password."; return; }

  apiPost(ENDPOINTS.login, { login, password }, (err, res) => {
    if (err) { if (out) out.textContent = `Login failed: ${err}`; return; }
    userId = Number(res.id) || 0;
    if (userId < 1) { if (out) out.textContent = "User/Password combination incorrect"; return; }
    firstName = res.firstName || ""; lastName = res.lastName || "";
    saveCookie();
    location.hash = "#contacts";
  });
}

function doRegister() {
  const first = (document.getElementById("regFirstName") || {}).value || "";
  const last  = (document.getElementById("regLastName") || {}).value || "";
  const login = (document.getElementById("regLogin") || {}).value || "";
  const pass  = (document.getElementById("regPassword") || {}).value || "";
  const out   = document.getElementById("createResult");
  if (out) out.textContent = "";

  if (!first || !last || !login || !pass) { if (out) out.textContent = "Please fill all fields."; return; }

  apiPost(ENDPOINTS.registerUser, { firstName:first, lastName:last, login, password:pass }, (err, res) => {
    if (err) { if (out) out.textContent = `Create failed: ${err}`; return; }
    if (out) { out.style.color = "#0a7d32"; out.textContent = "Account created! Redirecting to login…"; }
    setTimeout(() => { location.hash = "#login"; }, 800);
  });
}

function doLogout() {
  userId = 0; firstName = ""; lastName = "";
  document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
  location.hash = "#login";
}

/* ========= CONTACTS CRUD ========= */
function addContact() {
  readCookie();
  const out = document.getElementById("colorAddResult");
  if (out) out.textContent = "";

  const first = (document.getElementById("addFirstName") || {}).value || "";
  const last  = (document.getElementById("addLastName") || {}).value || "";
  const phone = (document.getElementById("addPhone") || {}).value || "";
  const email = (document.getElementById("addEmail") || {}).value || "";
  if (!first || !last) { if (out) out.textContent = "First and last name are required."; return; }

  apiPost(ENDPOINTS.addContact, { userId, firstName:first, lastName:last, phone, email }, (err, res) => {
    if (err) { if (out) { out.style.color="#b00020"; out.textContent = `Add failed: ${err}`; } return; }
    if (out) { out.style.color="#0a7d32"; out.textContent = "Contact added."; }
    const form = document.getElementById("addForm"); if (form?.reset) form.reset();
    searchContacts();
  });
}

let searchTimer = null;
function searchContacts() {
  readCookie();
  const srch = (document.getElementById("searchText") || {}).value || "";
  const out  = document.getElementById("colorSearchResult");
  if (out) out.textContent = "";

  if (searchTimer) clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    apiPost(ENDPOINTS.search, { userId, search: srch }, (err, res) => {
      if (err) { if (out) out.textContent = `Search failed: ${err}`; return; }
      const rows = Array.isArray(res.results) ? res.results : [];
      if (out) out.textContent = `${rows.length} result(s).`;
      renderContactRows(rows);
    });
  }, 100);
}

function renderContactRows(rows) {
  const tbody = document.getElementById("resultsBody");
  if (!tbody) return;
  tbody.innerHTML = "";

  rows.forEach((r) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td style="padding:8px;border-bottom:1px solid #eee;">${esc(r.firstName)}</td>
      <td style="padding:8px;border-bottom:1px solid #eee;">${esc(r.lastName)}</td>
      <td style="padding:8px;border-bottom:1px solid #eee;">${esc(r.phone)}</td>
      <td style="padding:8px;border-bottom:1px solid #eee;">${esc(r.email)}</td>
      <td style="padding:8px;border-bottom:1px solid #eee;">
        <button class="btn ghost" data-id="${Number(r.id) || 0}">Delete</button>
      </td>
    `;
    tbody.appendChild(tr);
  });

  tbody.querySelectorAll("button[data-id]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const id = Number(btn.getAttribute("data-id"));
      if (!id) return;
      if (!confirm("Delete this contact?")) return;
      deleteContact(id);
    });
  });
}

function deleteContact(contactId) {
  readCookie();
  apiPost(ENDPOINTS.delete, { contactId, userId }, (err, res) => {
    if (err) { alert(`Delete failed: ${err}`); return; }
    searchContacts();
  });
}

/* ========= COOKIES ========= */
function saveCookie() {
  const exp = new Date(Date.now() + COOKIE_TTL_MIN * 60 * 1000).toGMTString();
  document.cookie =
    "firstName=" + firstName +
    ",lastName=" + lastName +
    ",userId=" + userId +
    ";expires=" + exp;
}

function readCookie() {
  userId = -1; firstName = ""; lastName = "";
  const data = document.cookie || "";
  const splits = data.split(",");
  for (const s of splits) {
    const [k, vRaw] = s.trim().split("=");
    const v = (vRaw || "").trim();
    if (k === "firstName") firstName = v;
    else if (k === "lastName") lastName = v;
    else if (k === "userId") userId = parseInt(v || "-1");
  }
  return userId;
}

/* ========= HELPERS ========= */
function apiPost(url, body, cb) {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", url, true);
  xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
  xhr.onreadystatechange = function () {
    if (xhr.readyState !== 4) return;
    if (xhr.status !== 200) { cb(`HTTP ${xhr.status}`, null); return; }
    let json = {};
    try { json = JSON.parse(xhr.responseText || "{}"); }
    catch { cb("Invalid JSON from server", null); return; }
    if (json.error && json.error !== "") { cb(json.error, null); return; }
    cb(null, json);
  };
  xhr.send(JSON.stringify(body || {}));
}

function esc(s) {
  return String(s ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

