<script>
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
    components: {
    AppLayout,  // Register AppLayout component locally
  },
  data() {
    return {
      newTask: '',
      tasks: [],
    };
  },
  mounted() {
    this.fetchTasks();
  },
  methods: {
    async fetchTasks() {
      try {
        const response = await axios.get('/tasks');
        this.tasks = response.data;
      } catch (error) {
        console.error('Error fetching tasks:', error);
      }
    },
    async addTask() {
      if (!this.newTask.trim()) return;

      try {
        const response = await axios.post('/tasks', {
          description: this.newTask,
        });
        this.tasks.push(response.data);
        this.newTask = '';
      } catch (error) {
        console.error('Error adding task:', error);
      }
    },
    async toggleTask(task) {
      try {
        await axios.patch(`/tasks/${task.id}/toggle`);
        task.completed = !task.completed;
      } catch (error) {
        console.error('Error toggling task:', error);
      }
    },
  },
};
</script>


<style scoped>
/* Your custom styling here */
</style>

<template>
    <!-- Wrap Task page in AppLayout -->
    <AppLayout title="Task">
        <div class="min-h-screen bg-gray-100 p-6">
            <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-4">Task Manager</h1>

                <!-- Add Task Form -->
                <form @submit.prevent="addTask" class="flex items-center space-x-4 mb-6">
                    <input
                        v-model="newTask"
                        type="text"
                        placeholder="Enter a task description"
                        class="flex-grow p-2 border rounded-lg focus:ring focus:ring-blue-200 outline-none"
                    />
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        Add Task
                    </button>
                </form>

                <!-- Task List -->
                <ul v-if="tasks.length" class="space-y-4">
                    <li
                        v-for="task in tasks"
                        :key="task.id"
                        class="flex items-center justify-between p-4 bg-gray-50 rounded-lg shadow-sm"
                    >
                        <div class="flex items-center space-x-2">
                            <input
                                type="checkbox"
                                v-model="task.completed"
                                @change="toggleTask(task)"
                            />
                            <span
                                :class="{'line-through text-gray-500': task.completed}"
                                class="text-lg font-medium"
                            >
                                {{ task.description }}
                            </span>
                        </div>
                    </li>
                </ul>
                <p v-else class="text-gray-500">No tasks yet.</p>
            </div>
        </div>
    </AppLayout>
</template>


