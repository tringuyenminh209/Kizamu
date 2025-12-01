# Database Design Strategy - TodoApp (DOL LEAF)
## Cách thiết kế Database từ đầu

**Date**: December 2, 2025  
**Version**: 1.0

---

## 📋 Mục lục

1. [Nguyên tắc thiết kế](#nguyên-tắc-thiết-kế)
2. [Thứ tự thiết kế (Priority Order)](#thứ-tự-thiết-kế)
3. [Chi tiết từng bước](#chi-tiết-từng-bước)
4. [Best Practices](#best-practices)
5. [Checklist](#checklist)

---

## 🎯 Nguyên tắc thiết kế

### 1. **Core First, Features Later**
- Bắt đầu với **entities cốt lõi** (User, Task)
- Thêm features phụ sau (Analytics, AI, Learning)

### 2. **Dependency Order**
- Thiết kế bảng **không có foreign key** trước
- Sau đó thiết kế bảng **phụ thuộc** vào chúng

### 3. **Normalization**
- **3NF (Third Normal Form)** cho core tables
- **Denormalization** cho performance tables (stats, cache)

### 4. **Index Strategy**
- Index cho **foreign keys** ngay từ đầu
- Index cho **query patterns** thường dùng
- Composite indexes cho **multi-column queries**

---

## 🏗️ Thứ tự thiết kế

### **PHASE 1: Foundation (Core Entities)**
**Mục tiêu**: Tạo nền tảng cho toàn bộ hệ thống

```
1. users                    ← Bắt đầu từ đây (no dependencies)
2. user_profiles            ← 1:1 với users
3. user_settings            ← 1:1 với users
4. projects                 ← depends on users
5. tasks                    ← depends on users, projects (optional)
6. subtasks                 ← depends on tasks
7. tags                     ← independent
8. task_tags                ← junction table (tasks ↔ tags)
```

**Lý do**:
- `users` là root entity, tất cả đều phụ thuộc vào nó
- `tasks` là core business logic
- `projects` và `tags` là grouping mechanisms

---

### **PHASE 2: Learning & Education**
**Mục tiêu**: Hệ thống học tập và roadmap

```
9. learning_paths           ← depends on users
10. learning_milestones     ← depends on learning_paths
11. study_schedules          ← depends on learning_paths
12. timetable_classes       ← depends on users
13. timetable_studies       ← depends on timetable_classes, tasks
14. timetable_class_weekly_contents ← depends on timetable_classes
```

**Lý do**:
- Learning paths là long-term goals
- Milestones break down paths
- Timetable cho school/university context

---

### **PHASE 3: Focus & Productivity**
**Mục tiêu**: Tracking focus sessions và productivity

```
15. focus_sessions          ← depends on users, tasks
16. focus_environments      ← depends on tasks, focus_sessions
17. distraction_logs        ← depends on tasks, focus_sessions
18. context_switches        ← depends on users, tasks
19. task_abandonments      ← depends on users, tasks, focus_sessions
```

**Lý do**:
- Focus sessions là core productivity feature
- Environment và distractions track quality
- Context switches measure productivity cost

---

### **PHASE 4: Knowledge Base**
**Mục tiêu**: Personal knowledge management

```
20. knowledge_categories    ← depends on users (hierarchical)
21. knowledge_items         ← depends on knowledge_categories, users
22. knowledge_item_tags    ← junction table
```

**Lý do**:
- Knowledge base là independent feature
- Categories có thể hierarchical (parent_id)

---

### **PHASE 5: Code Learning Resources**
**Mục tiêu**: Cheat sheets và exercises

```
23. cheat_code_languages    ← independent (master data)
24. cheat_code_sections    ← depends on cheat_code_languages
25. code_examples           ← depends on cheat_code_sections
26. exercises               ← depends on cheat_code_languages
27. exercise_test_cases     ← depends on exercises
28. user_exercise_submissions ← depends on users, exercises
29. user_code_favorites     ← depends on users, code_examples
30. user_exercise_progress  ← depends on users, exercises
```

**Lý do**:
- Cheat code languages là master data (independent)
- Sections và examples là hierarchical
- User progress tracking riêng

---

### **PHASE 6: AI & Analytics**
**Mục tiêu**: AI features và analytics

```
31. ai_suggestions          ← depends on users, tasks (optional)
32. ai_interactions         ← depends on users
33. ai_summaries            ← depends on users
34. performance_metrics    ← depends on users
35. user_stats_cache        ← depends on users (denormalized)
```

**Lý do**:
- AI features là optional enhancements
- Stats cache là denormalized cho performance

---

### **PHASE 7: Communication & Notifications**
**Mục tiêu**: User communication

```
36. chat_conversations      ← depends on users
37. chat_messages           ← depends on chat_conversations, users
38. notifications           ← depends on users
```

**Lý do**:
- Chat và notifications là communication layer
- Có thể implement sau core features

---

### **PHASE 8: Daily Tracking**
**Mục tiêu**: Daily check-ins và reviews

```
39. daily_checkins          ← depends on users
40. daily_reviews           ← depends on users
```

**Lý do**:
- Daily tracking là analytics feature
- Có thể implement sau khi có tasks và focus sessions

---

### **PHASE 9: Templates**
**Mục tiêu**: Reusable templates

```
41. learning_path_templates      ← independent
42. learning_milestone_templates ← depends on learning_path_templates
43. task_templates              ← depends on learning_milestone_templates
```

**Lý do**:
- Templates là optional feature
- Giúp users tạo learning paths nhanh hơn

---

### **PHASE 10: Authentication & System**
**Mục tiêu**: Laravel built-in tables

```
44. password_reset_tokens   ← Laravel built-in
45. personal_access_tokens  ← Laravel Sanctum
46. sessions                ← Laravel sessions
47. cache                   ← Laravel cache
48. cache_locks             ← Laravel cache locks
```

**Lý do**:
- System tables, Laravel tự tạo
- Không cần thiết kế thủ công

---

## 📝 Chi tiết từng bước

### **STEP 1: Users Table (Foundation)**

```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email_verified_at TIMESTAMP NULL,
    language ENUM('vi', 'en', 'ja') DEFAULT 'ja',
    timezone VARCHAR(50) DEFAULT 'Asia/Tokyo',
    avatar_url VARCHAR(500) NULL,
    fcm_token VARCHAR(500) NULL,  -- For push notifications
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    
    INDEX idx_email (email),
    INDEX idx_fcm_token (fcm_token),
    INDEX idx_created_at (created_at)
);
```

**Quyết định thiết kế**:
- ✅ `fcm_token` cho push notifications (nullable)
- ✅ `language` và `timezone` cho localization
- ✅ Index trên `email` (unique lookup)
- ✅ Index trên `fcm_token` (notification queries)

---

### **STEP 2: Tasks Table (Core Business Logic)**

```sql
CREATE TABLE tasks (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    project_id BIGINT NULL,
    learning_milestone_id BIGINT NULL,
    
    -- Basic Info
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    category ENUM('study', 'work', 'personal', 'other') DEFAULT 'other',
    
    -- Priority & Energy
    priority TINYINT DEFAULT 3,  -- 1-5
    energy_level ENUM('low', 'medium', 'high') DEFAULT 'medium',
    
    -- Time Management
    estimated_minutes INT NULL,
    deadline TIMESTAMP NULL,
    scheduled_time TIME NULL,  -- HH:MM:SS format
    
    -- Status
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    is_abandoned BOOLEAN DEFAULT FALSE,
    
    -- AI Features
    ai_breakdown_enabled BOOLEAN DEFAULT FALSE,
    
    -- Deep Work Features
    requires_deep_focus BOOLEAN DEFAULT FALSE,
    allow_interruptions BOOLEAN DEFAULT TRUE,
    focus_difficulty INT DEFAULT 3,  -- 1-5
    
    -- Time Management
    warmup_minutes INT NULL,
    cooldown_minutes INT NULL,
    recovery_minutes INT NULL,
    
    -- Context Tracking
    last_focus_at TIMESTAMP NULL,
    last_active_at TIMESTAMP NULL,  -- Heartbeat tracking
    total_focus_minutes INT DEFAULT 0,
    distraction_count INT DEFAULT 0,
    abandonment_count INT DEFAULT 0,
    
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    FOREIGN KEY (learning_milestone_id) REFERENCES learning_milestones(id) ON DELETE CASCADE,
    
    INDEX idx_user_status (user_id, status),
    INDEX idx_project_status (project_id, status),
    INDEX idx_learning_milestone (learning_milestone_id),
    INDEX idx_deadline (deadline),
    INDEX idx_priority (priority),
    INDEX idx_user_created (user_id, created_at),
    INDEX idx_user_scheduled_time (user_id, scheduled_time),
    INDEX idx_status_last_active_at (status, last_active_at),
    INDEX idx_user_is_abandoned (user_id, is_abandoned)
);
```

**Quyết định thiết kế**:
- ✅ `scheduled_time` là `TIME` (không phải TIMESTAMP) - chỉ lưu giờ
- ✅ `is_abandoned` và `abandonment_count` cho abandonment tracking
- ✅ `last_active_at` cho heartbeat mechanism
- ✅ Composite indexes cho common queries
- ✅ Foreign keys với appropriate `ON DELETE` actions

---

### **STEP 3: Focus Sessions (Productivity Tracking)**

```sql
CREATE TABLE focus_sessions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    task_id BIGINT NOT NULL,
    
    session_type ENUM('work', 'break', 'long_break') DEFAULT 'work',
    duration_minutes INT NOT NULL,
    actual_minutes INT NULL,
    
    started_at TIMESTAMP NOT NULL,
    ended_at TIMESTAMP NULL,
    
    status ENUM('active', 'completed', 'paused', 'cancelled') DEFAULT 'active',
    notes TEXT NULL,
    quality_score TINYINT NULL,  -- 1-5
    
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    
    INDEX idx_user_started_at (user_id, started_at),
    INDEX idx_task_id (task_id),
    INDEX idx_user_status (user_id, status)
);
```

**Quyết định thiết kế**:
- ✅ Track cả `duration_minutes` (planned) và `actual_minutes` (actual)
- ✅ `quality_score` cho user feedback
- ✅ Index trên `(user_id, started_at)` cho time-based queries

---

## 🎨 Best Practices

### 1. **Naming Conventions**

```sql
-- Tables: snake_case, plural
users, tasks, focus_sessions

-- Columns: snake_case
user_id, created_at, is_abandoned

-- Indexes: descriptive names
idx_user_status, idx_task_abandoned_at

-- Foreign Keys: {table}_id
user_id, task_id, project_id
```

### 2. **Data Types**

```sql
-- IDs: BIGINT (auto increment)
id BIGINT PRIMARY KEY AUTO_INCREMENT

-- Text: VARCHAR vs TEXT
VARCHAR(255)  -- Short text (names, titles)
TEXT          -- Long text (descriptions, content)
LONGTEXT      -- Very long (code, markdown)

-- Enums: Use ENUM for fixed values
status ENUM('pending', 'in_progress', 'completed')

-- Booleans: BOOLEAN or TINYINT(1)
is_abandoned BOOLEAN DEFAULT FALSE

-- Timestamps: TIMESTAMP vs TIME
TIMESTAMP    -- Full datetime
TIME         -- Only time (HH:MM:SS)
DATE         -- Only date (YYYY-MM-DD)

-- JSON: For flexible data
data JSON NULL
```

### 3. **Foreign Keys & Constraints**

```sql
-- Always define ON DELETE behavior
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL

-- CASCADE: Delete related records
-- SET NULL: Set to NULL (for optional relationships)
-- RESTRICT: Prevent deletion if related records exist
```

### 4. **Indexes Strategy**

```sql
-- Single column indexes
INDEX idx_email (email)
INDEX idx_deadline (deadline)

-- Composite indexes (order matters!)
INDEX idx_user_status (user_id, status)  -- Query: WHERE user_id = ? AND status = ?
INDEX idx_status_last_active_at (status, last_active_at)  -- Query: WHERE status = ? ORDER BY last_active_at

-- Unique indexes
UNIQUE idx_user_email (email)
UNIQUE (user_id, code_example_id)  -- Composite unique
```

### 5. **Comments & Documentation**

```sql
-- Always add comments
title VARCHAR(255) NOT NULL COMMENT 'Task title',
is_abandoned BOOLEAN DEFAULT FALSE COMMENT 'Abandoned task flag',
last_active_at TIMESTAMP NULL COMMENT 'Last active time (heartbeat update)'
```

---

## ✅ Checklist

### Phase 1: Foundation
- [ ] `users` table với authentication fields
- [ ] `user_profiles` table (1:1)
- [ ] `user_settings` table (1:1)
- [ ] `projects` table
- [ ] `tasks` table với all features
- [ ] `subtasks` table
- [ ] `tags` và `task_tags` tables

### Phase 2: Learning
- [ ] `learning_paths` table
- [ ] `learning_milestones` table
- [ ] `study_schedules` table
- [ ] `timetable_*` tables

### Phase 3: Focus & Productivity
- [ ] `focus_sessions` table
- [ ] `focus_environments` table
- [ ] `distraction_logs` table
- [ ] `context_switches` table
- [ ] `task_abandonments` table

### Phase 4: Knowledge Base
- [ ] `knowledge_categories` table (hierarchical)
- [ ] `knowledge_items` table
- [ ] `knowledge_item_tags` table

### Phase 5: Code Resources
- [ ] `cheat_code_languages` table
- [ ] `cheat_code_sections` table
- [ ] `code_examples` table
- [ ] `exercises` và related tables

### Phase 6: AI & Analytics
- [ ] `ai_suggestions` table
- [ ] `ai_interactions` table
- [ ] `ai_summaries` table
- [ ] `performance_metrics` table
- [ ] `user_stats_cache` table

### Phase 7: Communication
- [ ] `chat_conversations` table
- [ ] `chat_messages` table
- [ ] `notifications` table

### Phase 8: Daily Tracking
- [ ] `daily_checkins` table
- [ ] `daily_reviews` table

### Phase 9: Templates
- [ ] `learning_path_templates` table
- [ ] `learning_milestone_templates` table
- [ ] `task_templates` table

### Phase 10: System
- [ ] Laravel built-in tables (auto-created)

---

## 🚀 Quick Start Guide

### Nếu bắt đầu từ đầu:

1. **Tạo migration cho `users`**
   ```bash
   php artisan make:migration create_users_table
   ```

2. **Tạo migration cho `tasks`**
   ```bash
   php artisan make:migration create_tasks_table
   ```

3. **Chạy migrations theo thứ tự**
   ```bash
   php artisan migrate
   ```

4. **Tạo models và relationships**
   ```bash
   php artisan make:model User
   php artisan make:model Task
   ```

5. **Test với seeders**
   ```bash
   php artisan make:seeder UserSeeder
   php artisan db:seed
   ```

---

## 📚 References

- [Laravel Migrations Documentation](https://laravel.com/docs/migrations)
- [Database Normalization](https://en.wikipedia.org/wiki/Database_normalization)
- [MySQL Index Best Practices](https://dev.mysql.com/doc/refman/8.0/en/optimization-indexes.html)

---

**Note**: Thứ tự này đảm bảo:
- ✅ Không có circular dependencies
- ✅ Foreign keys luôn reference đến tables đã tồn tại
- ✅ Core features hoạt động trước khi có advanced features
- ✅ Dễ dàng test và debug từng phase

