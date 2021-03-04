<template>
    <ul>
        {{ userName }}
        <v-divider></v-divider>
        {{ someData }}
        <v-divider></v-divider>
        {{ getFirst(3) }}
        <li v-for="todo in todos" :key="todo.text">
            <input :checked="todo.done" @change="toggle(todo)" type="checkbox">
            <span :class="{ done: todo.done }">{{ todo.text }}</span>
        </li>
        <li>
            <input @keyup.enter="addTodo" placeholder="What needs to be done?">
        </li>
    </ul>
</template>

<script>
import {mapGetters, mapMutations, mapState} from 'vuex'


export default {
    name: "todos",
    data() {
        return {
            name: 'Hasan',
            age: 25,

        }
    },
    computed: {
        userName() {
            return this.name + ' ' + this.age;
        },
        // todos() {
        //     return this.$store.state.todos.list
        // },
        ...mapState({
            todos: state => state.todos.list
        }),
        // localComputed() { /* ... */
        // },
        // mix this into the outer object with the object spread operator


        // someData() {
        //     return 'doneTodos : ' + this.$store.getters["todos/doneTodosCount"]
        // },
        ...mapGetters({
            someData: 'todos/doneTodosCount', // map `this.getFirst` to `this.$store.getters.doneTodosCount`
        }),
        // getFirst() {
        //     let id = 2
        //     return 'first todo: ' + this.$store.getters["todos/getTodoById"](id)
        // },

    },
    methods: {
        getFirst(id) {
            return 'first todo: ' + this.$store.getters["todos/getTodoById"](id)
        },
        addTodo(e) {
            this.$store.commit('todos/add', e.target.value)
            e.target.value = ''
        },
        ...mapMutations({
            toggle: 'todos/toggle'
        })
    }
}
</script>

<style scoped>
.done {
    text-decoration: line-through;
}
</style>