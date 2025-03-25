<div class="auth-container">
  <form class="auth-box" id="register-form" method="POST">
    <h2>Registo</h2>
    <input type="text" name="name" placeholder="Nome completo" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <select name="type_id" required>
      <option value="">Tipo de utilizador</option>
      <option value="1">Normal</option>
      <option value="2">Empresa</option>
    </select>
    <button type="submit">Registar</button>
    <div id="register-msg"></div>
    <p>Já tens conta? <a href="?page=login">Entrar</a></p>
  </form>
</div>