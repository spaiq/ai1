class Todo {
  constructor() {
    this.tasks = JSON.parse(localStorage.getItem("tasks")) || [];
    this.taskList = document.getElementById("task-list");
    this.searchInput = document.getElementById("search");
    this.newTaskInput = document.getElementById("new-task");
    this.taskDeadlineInput = document.getElementById("task-deadline");
    this.addTaskBtn = document.getElementById("add-task-btn");

    this.addTaskBtn.addEventListener("click", () => this.addTask());
    this.searchInput.addEventListener("input", () => this.draw());

    this.newTaskInput.addEventListener("keydown", (event) => {
      if (event.key === "Enter") {
        this.taskDeadlineInput.focus();
      }
    });

    this.taskDeadlineInput.addEventListener("keydown", (event) => {
      if (event.key === "Enter") {
        this.addTaskBtn.click();
      }
    });

    this.draw();
  }

  saveTasks() {
    localStorage.setItem("tasks", JSON.stringify(this.tasks));
  }

  draw() {
    this.taskList.innerHTML = "";
    const query = this.searchInput.value.toLowerCase();

    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    const oneWeekFromNow = new Date(today);
    oneWeekFromNow.setDate(today.getDate() + 7);

    const overdueTasks = [];
    const todayTasks = [];
    const tomorrowTasks = [];
    const thisWeekTasks = [];
    const futureTasks = [];

    this.tasks
      .sort((a, b) => {
        if (!a.deadline) return 1;
        if (!b.deadline) return -1;
        return new Date(a.deadline) - new Date(b.deadline);
      })
      .forEach((task, index) => {
        if (query.length >= 2 && !task.text.toLowerCase().includes(query)) {
          return;
        }

        const taskDate = new Date(task.deadline);
        const taskDay = new Date(
          taskDate.getFullYear(),
          taskDate.getMonth(),
          taskDate.getDate()
        );

        if (taskDay < today) {
          overdueTasks.push({ task, index });
        } else if (taskDay.toDateString() === today.toDateString()) {
          todayTasks.push({ task, index });
        } else if (
          taskDay.toDateString() ===
          new Date(today.getTime() + 24 * 60 * 60 * 1000).toDateString()
        ) {
          tomorrowTasks.push({ task, index });
        } else if (taskDay < oneWeekFromNow) {
          thisWeekTasks.push({ task, index });
        } else {
          futureTasks.push({ task, index });
        }
      });

    const addSection = (title, tasks) => {
      if (tasks.length > 0) {
        const section = document.createElement("div");
        section.innerHTML = `<h3>${title}</h3>`;
        tasks.forEach(({ task, index }) => {
          const li = document.createElement("li");
          const highlightedText = task.text.replace(
            new RegExp(`(${query})`, "gi"),
            '<mark class="highlight">$1</mark>'
          );
          li.innerHTML = `
                        <span class="task-text">${highlightedText}</span>
                        <span class="task-deadline">${
                          task.deadline
                            ? new Date(task.deadline).toLocaleString()
                            : "Brak terminu"
                        }</span>
                        <span class="delete-btn">🗑️</span>
                    `;
          section.appendChild(li);

          li.querySelector(".task-text").addEventListener("click", () =>
            this.editTask(index)
          );
          li.querySelector(".task-deadline").addEventListener("click", () =>
            this.editDeadline(index)
          );
          li.querySelector(".delete-btn").addEventListener("click", () =>
            this.deleteTask(index)
          );
        });
        this.taskList.appendChild(section);
      }
    };

    const getDayName = (date) => {
      return date.toLocaleDateString("pl-PL", { weekday: "long" });
    };

    addSection("Ups... minął termin zadania", overdueTasks);
    addSection(`Dzisiaj, ${getDayName(today)}`, todayTasks);
    addSection(
      `Jutro, ${getDayName(new Date(today.getTime() + 24 * 60 * 60 * 1000))}`,
      tomorrowTasks
    );

    const thisWeekSections = {};

    thisWeekTasks.forEach(({ task, index }) => {
      const taskDate = new Date(task.deadline);
      const dayName = getDayName(taskDate);
      if (!thisWeekSections[dayName]) {
        thisWeekSections[dayName] = [];
      }
      thisWeekSections[dayName].push({ task, index });
    });

    Object.keys(thisWeekSections)
      .sort()
      .forEach((dayName) => {
        addSection(dayName, thisWeekSections[dayName]);
      });

    addSection("Później", futureTasks);
  }

  addTask() {
    const text = this.newTaskInput.value.trim();
    const deadline = this.taskDeadlineInput.value;

    if (text.length < 3 || text.length > 255) {
      alert("Zadanie musi mieć od 3 do 255 znaków.");
      return;
    }

    if (deadline && new Date(deadline) <= new Date()) {
      alert("Data musi być w przyszłości.");
      return;
    }

    this.tasks.push({ text, deadline });
    this.saveTasks();
    this.draw();

    this.newTaskInput.value = "";
    this.taskDeadlineInput.value = "";
  }

  deleteTask(index) {
    this.tasks.splice(index, 1);
    this.saveTasks();
    this.draw();
  }

  editElement(index, selector, inputType, value, onSave) {
    const liElements = Array.from(this.taskList.querySelectorAll("li"));
    const li = liElements[index];
    const element = li.querySelector(selector);

    if (li.contains(element)) {
      const input = document.createElement("input");
      input.type = inputType;
      input.value = value;
      li.replaceChild(input, element);
      input.focus();

      input.addEventListener("blur", () => {
        onSave(input.value);
        this.saveTasks();
        this.draw();
      });

      input.addEventListener("keydown", (event) => {
        if (event.key === "Enter") {
          input.blur();
        }
      });
    } else {
      console.error(`${selector} element not found in the list item`);
    }
  }

  editTask(index) {
    const task = this.tasks[index];
    this.editElement(index, ".task-text", "text", task.text, (newValue) => {
      task.text = newValue;
    });
  }

  editDeadline(index) {
    const task = this.tasks[index];
    const deadlineValue = task.deadline
      ? new Date(
          new Date(task.deadline).getTime() -
            new Date().getTimezoneOffset() * 60000
        )
          .toISOString()
          .slice(0, 16)
      : "";
    this.editElement(
      index,
      ".task-deadline",
      "datetime-local",
      deadlineValue,
      (newValue) => {
        task.deadline = newValue;
      }
    );
  }
}

document.addEventListener("DOMContentLoaded", () => {
  new Todo();
});

document.addEventListener("mousemove", (e) => {
  const cursor = document.querySelector(".blob");
  cursor.style.transform = `translate3d(calc(${e.clientX}px - 50%), calc(${e.clientY}px - 50%), 0)`;
});
