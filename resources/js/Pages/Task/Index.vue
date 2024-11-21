<template>
    <AppLayout title="Tasks">
        <div class="flex justify-center">
            <div class="w-full max-w-md">
                <h1 class="text-lg font-bold mb-4 text-center">My Tasks</h1>

                <!-- Task Creation Form -->
                <form @submit.prevent="submitTask" class="mb-4">
                    <div class="flex">
                        <input
                            v-model="newTask"
                            type="text"
                            placeholder="Enter a new task"
                            class="flex-1 border px-4 py-2 rounded-l"
                        />


                        <button
                            type="submit"
                            class="bg-blue-500 text-white px-4 py-2 rounded-r shadow hover:bg-blue-600 transition duration-200"
                        >
                            Add Task
                        </button>
                    </div>
                </form>

                <!-- Task List -->
                <ul v-if="tasks.length" class="space-y-2">
                    <li
                        v-for="task in tasks"
                        :key="task.id"
                        class="flex justify-between items-center border p-2 rounded"
                    >
                        <div class="flex items-center">
                            <!-- Task Checkbox -->
                            <input
                                type="checkbox"
                                :checked="task.completed"
                                @change="toggleTask(task)"
                                class="mr-2"
                            />

                            <!-- Task Description or Edit Input -->
                            <div>
                                <input
                                    v-if="task.editing"
                                    v-model="task.description"
                                    @blur="saveEdit(task)"
                                    @keyup.enter="saveEdit(task)"
                                    class="border px-2 py-1 rounded"
                                />
                                <span
                                    v-else
                                    :class="{ 'line-through': task.completed }"
                                >
                                    {{ task.description }}
                                </span>
                            </div>
                        </div>

                        <!-- Edit and Delete Buttons -->
                        <div class="flex space-x-2">
                            <button
                                v-if="!task.editing"
                                @click="startEdit(task)"
                                class="bg-yellow-500 text-white px-2 py-1 rounded shadow hover:bg-yellow-600 transition duration-200"
                            >
                                Edit
                            </button>
                            <button
                                v-if="task.editing"
                                @click="saveEdit(task)"
                                class="bg-green-500 text-white px-2 py-1 rounded shadow hover:bg-green-600 transition duration-200"
                            >
                                Save
                            </button>
                            <button
                                @click="deleteTask(task)"
                                class="bg-red-500 text-white px-2 py-1 rounded shadow hover:bg-red-600 transition duration-200"
                            >
                                Delete
                            </button>
                        </div>
                    </li>
                </ul>
                <p v-else class="text-center">No tasks available.</p>
            </div>
        </div>
    </AppLayout>
</template>




<script>
import axios from 'axios';
import { reactive } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
    props: {
        tasks: Array, // Received from the backend
    },
    components: {
        AppLayout,
    },
    data() {
        return {
            newTask: '', // For creating new tasks
            localTasks: reactive([...this.tasks]), // Local reactive copy of tasks
        };
    },
    computed: {
        tasks() {
            return this.localTasks; // Access local tasks in the template
        },
    },
    methods: {
        // Create a new task
        async submitTask() {
            if (!this.newTask.trim()) {
                alert('Task description is required.');
                return;
            }

            try {
                const response = await axios.post('/task/store', {
                    description: this.newTask,
                });
                this.localTasks.push(response.data);
                this.newTask = '';
            } catch (error) {
                console.error('Error creating task:', error);
                alert('Failed to add the task. Please try again.');
            }
        },

        // Toggle task completion
        async toggleTask(task) {
            try {
                const response = await axios.patch(`/task/${task.id}/toggle`, {
                    completed: !task.completed,
                });
                task.completed = response.data.completed;
            } catch (error) {
                console.error('Error toggling task completion:', error);
                alert('Failed to update the task. Please try again.');
            }
        },

        // Start editing a task
        startEdit(task) {
            task.editing = true; // Enable inline editing
        },

        // Save the edited task
        async saveEdit(task) {
            try {
                const response = await axios.patch(`/task/${task.id}`, {
                    description: task.description,
                });
                Object.assign(task, response.data); // Sync the task with backend response
                task.editing = false; // Disable editing mode
            } catch (error) {
                console.error('Error saving task:', error);
                alert('Failed to save the task. Please try again.');
            }
        },

        // Delete a task
        async deleteTask(task) {
            if (!confirm('Are you sure you want to delete this task?')) return;

            try {
                await axios.delete(`/task/${task.id}`);
                this.localTasks.splice(this.localTasks.indexOf(task), 1); // Remove task from local list
            } catch (error) {
                console.error('Error deleting task:', error);
                alert('Failed to delete the task. Please try again.');
            }
        },
    },
};
</script>



<style>
.line-through {
    text-decoration: line-through;
}
</style>
