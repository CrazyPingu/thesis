<template>
	<div>
		<h1>Carica Database</h1>
		<input type="button" value="Carica" @click="carica_database">
	</div>
</template>

<script>
export default {
	name: 'UpdateDb',
	methods: {
		carica_database() {
			console.log("Carica database");
		}
	}
}
</script>

<style scoped>

input[type=button]{
	background-color: #4CAF50;
	border: none;
	color: white;
	padding: 15px 32px;
	text-align: center;
	text-decoration: none;
	display: inline-block;
	font-size: 16px;
	margin: 4px 2px;
	cursor: pointer;
	margin: 3vh 0;
}

</style>
