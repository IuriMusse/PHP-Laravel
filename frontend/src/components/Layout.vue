<template>
  <div>
    <nav class="navbar">
      <div class="navbar-logo">Desafio Técnico - PHP Laravel + Vue.js</div>
      <ul class="navbar-links">
        <li><router-link to="/">Usuários</router-link></li>
        <li><router-link to="/create">Cadastrar</router-link></li>
      </ul>
    </nav>

    <main class="content">
      <router-view />
    </main>
  </div>
</template>

<style scoped></style>
