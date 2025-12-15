# 📊 Tài liệu Database Schema - ToDoApp

## 📑 Mục lục

1. [Tổng quan hệ thống](#tổng-quan-hệ-thống)
2. [Core - Users & Authentication](#1-core---users--authentication)
3. [User Settings & Profiles](#2-user-settings--profiles)
4. [Learning Paths & Milestones](#3-learning-paths--milestones)
5. [Projects & Tasks](#4-projects--tasks)
6. [Focus & Productivity](#5-focus--productivity)
7. [Daily Check-ins & Reviews](#6-daily-check-ins--reviews)
8. [AI Features](#7-ai-features)
9. [Statistics & Metrics](#8-statistics--metrics)
10. [Notifications](#9-notifications)
11. [Knowledge Management](#10-knowledge-management)
12. [Timetable Management](#11-timetable-management)
13. [Chat Conversations](#12-chat-conversations)
14. [Cheat Code / Code Learning Platform](#13-cheat-code--code-learning-platform)
15. [Cache Tables](#14-cache-tables)

---

## Tổng quan hệ thống

**ToDoApp** là một ứng dụng quản lý công việc và học tập thông minh tích hợp AI, bao gồm:

- **49 bảng** dữ liệu
- **62 mối quan hệ** foreign key
- **Framework**: Laravel 11 với Eloquent ORM
- **Database**: MySQL/MariaDB
- **Tính năng chính**:
  - Quản lý task & project
  - Lộ trình học tập (Learning Paths)
  - Deep Focus & Pomodoro
  - AI Coaching & Suggestions
  - Knowledge Base với Spaced Repetition
  - Timetable & Study Management
  - Code Learning Platform

---

## 1. Core - Users & Authentication

### 🎯 Mục đích chính
Quản lý người dùng, xác thực và phiên làm việc của ứng dụng.

### 📋 Các bảng

#### `users` - Bảng người dùng chính
**Chức năng**: Lưu trữ thông tin tài khoản người dùng

**Các trường quan trọng**:
- `email`: Email đăng nhập (unique)
- `password`: Mật khẩu đã hash
- `language`: Ngôn ngữ giao diện (vi, en, ja)
- `timezone`: Múi giờ người dùng
- `avatar_url`: Link ảnh đại diện

**Use cases**:
- Đăng ký tài khoản mới
- Đăng nhập/đăng xuất
- Quản lý profile cơ bản
- Đa ngôn ngữ (Vietnamese, English, Japanese)

#### `password_reset_tokens` & `password_resets`
**Chức năng**: Quản lý việc đặt lại mật khẩu

**Quy trình**:
1. User yêu cầu reset password → tạo token
2. Gửi email với token
3. Xác thực token và cho phép đổi mật khẩu

#### `sessions`
**Chức năng**: Lưu trữ phiên làm việc HTTP của Laravel

**Thông tin lưu trữ**:
- User ID
- IP address
- User agent
- Session payload
- Thời gian hoạt động cuối

#### `personal_access_tokens`
**Chức năng**: Quản lý API tokens cho mobile app và third-party integrations

**Tính năng**:
- Tạo token cho mobile app
- Phân quyền (abilities)
- Token expiration
- Tracking last used

---

## 2. User Settings & Profiles

### 🎯 Mục đích chính
Lưu trữ thông tin cá nhân hóa và cài đặt của người dùng.

### 📋 Các bảng

#### `user_profiles`
**Chức năng**: Lưu thông tin profile và onboarding

**Các trường quan trọng**:
- `goal_type`: Mục tiêu chính (learning, work, health)
- `preferred_time`: Thời gian làm việc ưa thích (morning, afternoon, evening)
- `notification_enabled`: Bật/tắt thông báo
- `onboarding_completed`: Đã hoàn thành onboarding chưa

**Use cases**:
- Personalize trải nghiệm người dùng
- Hiển thị onboarding cho user mới
- Đề xuất thời gian làm việc phù hợp

#### `user_settings`
**Chức năng**: Cài đặt chi tiết cho Pomodoro, notifications, theme

**Các nhóm setting**:

**1. Pomodoro Settings**:
- `pomodoro_duration`: Thời gian Pomodoro (default: 25 phút)
- `break_minutes`: Thời gian nghỉ ngắn (default: 5 phút)
- `long_break_minutes`: Thời gian nghỉ dài (default: 15 phút)
- `auto_start_break`: Tự động bắt đầu break

**2. Focus Settings**:
- `default_focus_minutes`: Thời gian focus mặc định
- `block_notifications`: Chặn notification khi focus
- `background_sound`: Phát nhạc nền khi focus

**3. Task Settings**:
- `daily_target_tasks`: Mục tiêu số task mỗi ngày (default: 3)

**4. Notification Settings**:
- `notification_enabled`: Bật/tắt notification
- `push_notifications`: Push notifications
- `daily_reminders`: Nhắc nhở hàng ngày
- `goal_reminders`: Nhắc nhở về mục tiêu
- `reminder_times`: JSON array các thời điểm nhắc nhở

**5. Localization**:
- `language`: Ngôn ngữ (vi, en, ja)
- `timezone`: Múi giờ
- `theme`: Light/Dark/Auto

---

## 3. Learning Paths & Milestones

### 🎯 Mục đích chính
Quản lý lộ trình học tập dài hạn với các milestone và template có sẵn.

### 📋 Các bảng

#### `learning_paths`
**Chức năng**: Lưu trữ các lộ trình học tập của người dùng

**Các trường quan trọng**:
- `title`: Tên lộ trình (VD: "Học Java Full Stack")
- `goal_type`: Loại mục tiêu (career, skill, certification, hobby)
- `status`: Trạng thái (active, paused, completed, abandoned)
- `progress_percentage`: Phần trăm hoàn thành (0-100)
- `is_ai_generated`: Lộ trình do AI tạo hay tự tạo
- `ai_prompt`: Prompt user đã dùng để AI tạo roadmap
- `estimated_hours_total`: Tổng số giờ ước tính
- `actual_hours_total`: Tổng số giờ thực tế đã học
- `tags`: Tags phân loại (JSON array)
- `color` & `icon`: Tùy chỉnh giao diện

**Use cases**:
- Tạo lộ trình học tập dài hạn (VD: "Trở thành Java Developer")
- AI tự động tạo roadmap từ mục tiêu của user
- Tracking tiến độ học tập
- Ước tính thời gian học vs thực tế

#### `learning_milestones`
**Chức năng**: Chia nhỏ learning path thành các milestone

**Cấu trúc**:
- Mỗi milestone là một giai đoạn trong lộ trình học
- Có thứ tự (`sort_order`)
- Có trạng thái riêng (pending, in_progress, completed, skipped)
- Tracking tiến độ riêng

**Ví dụ Learning Path**: "Học Java Full Stack"
- Milestone 1: "Java Basics" (completed)
- Milestone 2: "OOP in Java" (in_progress)
- Milestone 3: "Spring Boot" (pending)
- Milestone 4: "Database & JPA" (pending)

**Các trường quan trọng**:
- `deliverables`: Sản phẩm cần hoàn thành (JSON)
- `self_assessment`: Tự đánh giá (1-5 sao)
- `notes`: Ghi chú học tập

#### `learning_path_templates`
**Chức năng**: Template lộ trình học có sẵn

**Tính năng**:
- Admin/hệ thống tạo sẵn các lộ trình học phổ biến
- User có thể chọn template và customize
- Phân loại theo category (programming, design, business, language, data_science)
- Phân loại theo difficulty (beginner, intermediate, advanced)
- Featured templates
- Tracking usage count

**Ví dụ templates**:
- "Web Development với Laravel"
- "Data Science với Python"
- "Mobile Development với Flutter"
- "DevOps Engineer Roadmap"

#### `learning_milestone_templates`
**Chức năng**: Template các milestone cho learning path templates

**Mối quan hệ**:
```
learning_path_templates
  → learning_milestone_templates
    → task_templates
```

#### `task_templates`
**Chức năng**: Template các task cho milestone templates

**Đặc biệt**:
- `knowledge_items`: Nội dung học tập (notes, code examples, links, exercises) - JSON
- `resources`: Tài liệu học (links, videos)
- `subtasks`: Danh sách subtasks

**Workflow tạo lộ trình học từ template**:
1. User chọn template "Web Development với Laravel"
2. Hệ thống copy template → tạo learning_path mới
3. Copy tất cả milestone_templates → tạo learning_milestones
4. Copy tất cả task_templates → tạo tasks thực tế
5. User bắt đầu học theo roadmap

---

## 4. Projects & Tasks

### 🎯 Mục đích chính
Quản lý công việc hàng ngày với hệ thống task, project và tags.

### 📋 Các bảng

#### `projects`
**Chức năng**: Nhóm các task liên quan thành project

**Đặc điểm**:
- Đa ngôn ngữ (name_en, name_ja, description_en, description_ja)
- Progress tracking
- Tùy chỉnh màu sắc
- Có start_date và end_date
- Có thể archive (is_active = false)

**Use cases**:
- Quản lý dự án lớn
- Nhóm các task liên quan
- Tracking tiến độ dự án

#### `tasks`
**Chức năng**: Bảng trung tâm - quản lý task với deep work features

**Cấu trúc mạnh mẽ với nhiều tính năng**:

**1. Basic Task Info**:
- `title`, `description`
- `category`: study, work, personal, other
- `priority`: 1-5 (5 là cao nhất)
- `status`: pending, in_progress, completed, cancelled
- `deadline` & `scheduled_time`

**2. Task Relationships**:
- `project_id`: Thuộc project nào (nullable)
- `learning_milestone_id`: Thuộc milestone học tập nào (nullable)
- `user_id`: Người sở hữu task

**3. Deep Work Features** (Tính năng độc đáo):
- `energy_level`: Mức năng lượng cần (low, medium, high)
- `requires_deep_focus`: Task cần deep work không
- `allow_interruptions`: Cho phép bị gián đoạn không
- `focus_difficulty`: Độ khó tập trung (1-5)
  - 1-2: Shallow work (có thể làm khi mệt)
  - 3: Medium focus
  - 4-5: Ultra-deep focus (cần tinh thần tốt nhất)

**4. Time Management**:
- `estimated_minutes`: Ước tính thời gian
- `warmup_minutes`: Thời gian khởi động trước task
- `cooldown_minutes`: Thời gian hạ nhiệt sau task
- `recovery_minutes`: Thời gian phục hồi sau khi hoàn thành

**5. AI Features**:
- `ai_breakdown_enabled`: Cho phép AI phân tích task

**6. Focus Tracking**:
- `last_focus_at`: Lần focus cuối
- `total_focus_minutes`: Tổng thời gian đã focus
- `distraction_count`: Số lần bị distract

**Use cases**:
- Tạo task hàng ngày
- Lên kế hoạch task theo năng lượng (làm deep work vào buổi sáng, shallow work buổi chiều)
- AI breakdown task phức tạp thành subtasks
- Tracking thời gian focus thực tế

#### `subtasks`
**Chức năng**: Chia nhỏ task lớn thành các bước nhỏ

**Đặc điểm**:
- Có sort_order
- Có estimated_minutes riêng
- Checkbox is_completed
- Đơn giản, dễ track

#### `tags`
**Chức năng**: Tags để phân loại tasks

**Tính năng**:
- Tên tag unique
- Màu sắc tùy chỉnh
- Icon tùy chỉnh

**Ví dụ tags**:
- #urgent
- #backend
- #learning
- #side-project

#### `task_tags`
**Chức năng**: Many-to-many relationship giữa tasks và tags

**Cho phép**:
- Một task có nhiều tags
- Một tag được dùng cho nhiều tasks
- Filter tasks theo tags
- Group tasks theo tags

---

## 5. Focus & Productivity

### 🎯 Mục đích chính
Hỗ trợ deep work với Pomodoro, tracking focus sessions và distractions.

### 📋 Các bảng

#### `focus_sessions`
**Chức năng**: Ghi lại các phiên làm việc Pomodoro

**Workflow Pomodoro**:
1. User bắt đầu focus session cho một task
2. Hệ thống tạo record với status = 'active'
3. Timer đếm ngược (duration_minutes)
4. User có thể pause/cancel
5. Khi hoàn thành → status = 'completed', lưu actual_minutes
6. User đánh giá quality_score (1-5)

**Các loại session**:
- `work`: Phiên làm việc chính
- `break`: Nghỉ ngắn (5 phút)
- `long_break`: Nghỉ dài (15 phút)

**Tracking**:
- Thời gian bắt đầu/kết thúc thực tế
- So sánh planned vs actual duration
- Quality score để phân tích hiệu suất

#### `focus_environments`
**Chức năng**: Checklist môi trường trước khi bắt đầu deep work

**Các checkpoint**:
- `quiet_space`: Không gian yên tĩnh ✓
- `phone_silent`: Điện thoại im lặng ✓
- `materials_ready`: Tài liệu đã chuẩn bị ✓
- `water_coffee_ready`: Nước/cà phê sẵn sàng ✓
- `comfortable_position`: Tư thế ngồi thoải mái ✓
- `notifications_off`: Tắt thông báo ✓
- `apps_closed`: Các app/tab không cần thiết đã đóng ✓

**Mục đích**:
- Chuẩn bị tâm lý trước khi làm việc
- Tạo môi trường tối ưu cho deep work
- Giảm khả năng bị distract
- Thống kê: User có chuẩn bị kỹ thì quality_score cao hơn

#### `distraction_logs`
**Chức năng**: Ghi lại các lần bị phân tâm trong focus session

**Các loại distraction**:
- `phone`: Điện thoại
- `social_media`: Mạng xã hội
- `noise`: Tiếng ồn
- `person`: Người khác
- `thoughts`: Suy nghĩ
- `hunger_thirst`: Đói/khát
- `fatigue`: Mệt mỏi
- `other`: Khác

**Thông tin ghi lại**:
- Thời gian xảy ra
- Thời điểm trong ngày (time_of_day)
- Kéo dài bao lâu (duration_seconds)
- Ghi chú

**Phân tích**:
- Loại distraction nào phổ biến nhất?
- Thời điểm nào trong ngày dễ bị distract?
- Đưa ra đề xuất cải thiện

#### `context_switches`
**Chức năng**: Tracking việc chuyển đổi giữa các task

**Context Switch Cost**:
- Nghiên cứu cho thấy: Mất ~23 phút để lấy lại focus sau khi switch task
- Nếu switch giữa các task khác category hoặc focus level → significant switch
- Hệ thống cảnh báo user về chi phí switching

**Thông tin lưu trữ**:
- Task cũ (from_task_id, from_category, from_focus_difficulty)
- Task mới (to_task_id, to_category, to_focus_difficulty)
- Có phải significant switch không
- Estimated cost (thời gian mất để recover)
- User có tiếp tục không (user_proceeded)

**Use cases**:
- Cảnh báo khi switch từ deep work sang shallow work
- Thống kê số lần context switch mỗi ngày
- Đề xuất group tasks cùng loại để giảm switching

---

## 6. Daily Check-ins & Reviews

### 🎯 Mục đích chính
Theo dõi sức khỏe tinh thần, năng suất và reflection hàng ngày.

### 📋 Các bảng

#### `daily_checkins`
**Chức năng**: Morning check-in để lên kế hoạch ngày mới

**Workflow buổi sáng**:
1. User mở app vào buổi sáng
2. Điền morning check-in:
   - Mức năng lượng hôm nay (low, medium, high)
   - Mood score (1-5)
   - Số giờ ngủ đêm qua
   - Stress level (low, medium, high)
3. Viết goals và priorities cho hôm nay (JSON array)
4. AI có thể tạo suggestions dựa trên energy level

**Tính năng AI**:
- Nếu energy = low, sleep_hours < 6 → AI suggest làm shallow tasks
- Nếu energy = high → AI suggest làm deep work tasks quan trọng
- AI đề xuất schedule tối ưu cho ngày

**Use cases**:
- Lên kế hoạch ngày dựa trên trạng thái hiện tại
- Tracking sleep patterns
- Correlation giữa sleep và productivity

#### `daily_reviews`
**Chức năng**: Evening reflection - nhìn lại ngày đã qua

**Workflow buổi tối**:
1. User mở app vào cuối ngày
2. Hệ thống tự động tính:
   - Số tasks hoàn thành
   - Tổng focus time
3. User tự đánh giá:
   - Productivity score (1-10)
   - Focus time score
   - Task completion score
   - Goal achievement score
   - Work-life balance score
4. Viết reflection:
   - Achievements: Thành tựu hôm nay
   - Gratitude: Biết ơn điều gì
   - Challenges: Khó khăn gặp phải
   - Lessons learned: Bài học rút ra
   - Tomorrow goals: Mục tiêu ngày mai

**Phân tích dài hạn**:
- Xu hướng mood theo thời gian
- Correlation giữa sleep và productivity
- Những ngày nào năng suất nhất
- Pattern nhận ra (VD: Thứ 2 luôn productivity thấp)

---

## 7. AI Features

### 🎯 Mục đích chính
Tích hợp AI để coaching, gợi ý và tự động hóa.

### 📋 Các bảng

#### `ai_suggestions`
**Chức năng**: Lưu các gợi ý AI đưa ra cho user

**Các loại suggestion**:
1. **task_breakdown**: AI phân tích task phức tạp thành subtasks
   - Input: Task title, description
   - Output: Danh sách subtasks với estimated time

2. **daily_plan**: AI tạo kế hoạch ngày dựa trên:
   - Energy level từ daily_checkin
   - Tasks pending
   - Priorities

3. **smart_schedule**: AI sắp xếp thời gian biểu tối ưu
   - Deep work tasks vào lúc energy cao
   - Shallow tasks vào lúc energy thấp
   - Respect deadlines

4. **motivational**: AI gửi lời động viên
   - Khi streak giảm
   - Khi hoàn thành milestone
   - Khi cần boost motivation

**Feedback loop**:
- User có thể accept/reject suggestion
- Có thể rate (feedback_score 1-5)
- AI học từ feedback để improve

#### `ai_interactions`
**Chức năng**: Log tất cả interactions với AI

**Thông tin lưu**:
- Loại interaction (breakdown, suggestion, coach, reschedule)
- Input data (JSON)
- Response data (JSON)
- Processing time
- Success/failure

**Mục đích**:
- Debug AI issues
- Analyze AI performance
- Improve prompts
- Billing/usage tracking

#### `ai_summaries`
**Chức năng**: AI tạo summary định kỳ

**Các loại summary**:
1. **Daily summary**: Tóm tắt ngày
   - Tasks completed
   - Focus time
   - Highlights

2. **Weekly summary**: Tóm tắt tuần
   - Progress on learning paths
   - Productivity trends
   - Achievements
   - Areas to improve

3. **Monthly summary**: Tóm tắt tháng
   - Big picture progress
   - Milestones completed
   - Habits formed
   - Goals for next month

**Use cases**:
- User xem lại progress nhanh chóng
- Email weekly report
- Share on social media

---

## 8. Statistics & Metrics

### 🎯 Mục đích chính
Theo dõi metrics và phân tích xu hướng năng suất.

### 📋 Các bảng

#### `user_stats`
**Chức năng**: Snapshot statistics mỗi ngày

**Metrics hàng ngày**:
- Tasks completed today
- Focus minutes today
- Streak days (số ngày liên tục active)
- Productivity score
- Average mood
- Average energy

**Use cases**:
- Dashboard hiển thị stats hôm nay
- Graph xu hướng theo thời gian
- Gamification (streaks, badges)

#### `performance_metrics`
**Chức năng**: Time-series metrics chi tiết hơn

**Các loại metric**:
1. **daily_completion**: % tasks hoàn thành mỗi ngày
2. **focus_time**: Tổng thời gian focus
3. **mood_trend**: Xu hướng mood
4. **streak_maintenance**: Duy trì streak

**Trend analysis**:
- `trend_direction`: up, down, stable
- So sánh với tuần/tháng trước
- Predict future trends

**Use cases**:
- Analytics dashboard
- Identify patterns
- Set realistic goals based on historical data

#### `activity_logs`
**Chức năng**: Audit log mọi hành động trong hệ thống

**Các action được log**:
- `task.created`, `task.updated`, `task.completed`
- `session.started`, `session.completed`
- `learning_path.created`, `milestone.completed`
- `settings.updated`

**Metadata lưu trữ**:
- IP address
- User agent (thiết bị nào)
- Additional data (JSON)

**Use cases**:
- Security audit
- Debug issues
- User behavior analysis
- GDPR compliance (user data export)

---

## 9. Notifications

### 🎯 Mục đích chính
Hệ thống thông báo đa dạng cho user.

### 📋 Bảng `notifications`

**Các loại notification**:
1. **reminder**: Nhắc nhở về task, deadline
   - "Task 'Học Laravel' sắp deadline trong 1 giờ"
   - "Bạn có 3 tasks scheduled lúc 14:00"

2. **achievement**: Thành tựu, milestone
   - "Chúc mừng! Bạn đã hoàn thành milestone 'Java Basics'"
   - "Streak 7 ngày! Tuyệt vời!"

3. **motivational**: Động viên
   - "Hãy bắt đầu ngày mới với năng lượng tích cực!"
   - "Bạn đã làm rất tốt tuần này!"

4. **system**: Thông báo hệ thống
   - "App đã cập nhật version mới"
   - "Maintenance schedule"

**Scheduling**:
- `scheduled_at`: Thời điểm gửi (có thể schedule trước)
- `sent_at`: Thời điểm đã gửi thực tế
- `is_read`: Đã đọc chưa

**Delivery channels** (có thể mở rộng):
- In-app notification
- Push notification (mobile)
- Email (weekly summary)

---

## 10. Knowledge Management

### 🎯 Mục đích chính
Hệ thống quản lý kiến thức với spaced repetition, giống Notion + Anki.

### 📋 Các bảng

#### `knowledge_categories`
**Chức năng**: Cấu trúc phân cấp để organize knowledge

**Đặc điểm**:
- Hierarchical (có parent_id → tạo cây)
- Màu sắc, icon tùy chỉnh
- Sort order
- Item count (số items trong category)

**Ví dụ cấu trúc**:
```
📚 Programming
  ├── 💻 Java
  │   ├── Basics
  │   ├── OOP
  │   └── Spring Boot
  ├── 🐘 PHP
  │   ├── Laravel
  │   └── Symfony
  └── 🐍 Python
      ├── Django
      └── Data Science

📖 Languages
  ├── 🇯🇵 Japanese
  │   ├── JLPT N5
  │   └── JLPT N4
  └── 🇬🇧 English
```

#### `knowledge_items`
**Chức năng**: Lưu trữ kiến thức đa dạng với spaced repetition

**Các loại item**:
1. **note**: Ghi chú (Markdown)
   - Theories, concepts
   - Personal notes

2. **code_snippet**: Code examples
   - `code_language`: java, php, python...
   - `content`: Source code
   - Syntax highlighting

3. **resource_link**: Liên kết tài liệu
   - `url`: Link to article, video, course
   - `content`: Description

4. **exercise**: Bài tập
   - `question`: Câu hỏi
   - `answer`: Đáp án (có thể ẩn)
   - `difficulty`: easy, medium, hard

5. **attachment**: File đính kèm
   - `attachment_path`: Path to file
   - `attachment_mime`: File type
   - `attachment_size`: Size in bytes

**Spaced Repetition System**:
- `review_count`: Đã review bao nhiêu lần
- `last_reviewed_at`: Review lần cuối khi nào
- `next_review_date`: Ngày cần review tiếp theo
- `retention_score`: Độ nhớ (1-5)
  - 5: Nhớ rất chắc → review sau 1 tháng
  - 3: Nhớ tạm → review sau 1 tuần
  - 1: Quên → review ngày mai

**Algorithm** (simplified):
```
if retention_score == 5:
    next_review = today + 30 days
elif retention_score == 4:
    next_review = today + 14 days
elif retention_score == 3:
    next_review = today + 7 days
else:
    next_review = today + 1 day
```

**AI Features**:
- `ai_summary`: AI tóm tắt nội dung dài

**Engagement tracking**:
- `view_count`: Số lần xem
- `is_favorite`: Đánh dấu yêu thích
- `is_archived`: Archive để dọn dẹp

**Relationships**:
- `learning_path_id`: Thuộc lộ trình học nào
- `source_task_id`: Được tạo từ task nào (VD: task "Học OOP" → tạo knowledge item "OOP concepts")

#### `knowledge_item_tags`
**Chức năng**: Tags cho knowledge items

**Use cases**:
- Tag #important, #review, #concept, #example
- Filter items by tags
- Quick search

---

## 11. Timetable Management

### 🎯 Mục đích chính
Quản lý thời khóa biểu học tập (dành cho học sinh, sinh viên).

### 📋 Các bảng

#### `timetable_classes`
**Chức năng**: Các lớp học trong thời khóa biểu

**Thông tin lớp học**:
- `name`: Tên môn học
- `instructor`: Giáo viên
- `room`: Phòng học
- `day`: Thứ mấy (monday - sunday)
- `period`: Tiết học (1-10)
- `start_time` & `end_time`: Giờ học
- `color` & `icon`: Tùy chỉnh hiển thị

**Relationship**:
- `learning_path_id`: Liên kết với learning path (VD: Class "Web Programming" → Learning path "Trở thành Web Developer")

**Use cases**:
- Tạo thời khóa biểu tuần
- Hiển thị lịch học
- Nhắc nhở trước giờ học

#### `timetable_studies`
**Chức năng**: Homework, bài tập, kiểm tra liên quan đến class

**Các loại**:
- `homework`: Bài tập về nhà
- `review`: Ôn tập
- `exam`: Kiểm tra
- `project`: Đồ án

**Workflow**:
1. Giáo viên giao bài tập cho môn "Web Programming"
2. Tạo record trong timetable_studies với type = 'homework'
3. Set due_date
4. Có thể tạo task tương ứng (task_id) để track

**Use cases**:
- Danh sách bài tập cần làm
- Calendar view deadlines
- Priority management

#### `timetable_class_weekly_contents`
**Chức năng**: Nội dung học mỗi tuần cho từng class

**Ví dụ**: Class "Web Programming"
- Week 1: HTML Basics
- Week 2: CSS Fundamentals
- Week 3: JavaScript Introduction
- Week 4: DOM Manipulation

**Thông tin mỗi tuần**:
- `title`: Chủ đề tuần
- `content`: Nội dung chi tiết
- `homework`: Bài tập về nhà
- `notes`: Ghi chú
- `status`: scheduled, completed, cancelled

**Use cases**:
- Giáo viên lên kế hoạch giảng dạy
- Học sinh xem nội dung sẽ học
- Review lại các tuần đã học

#### `study_schedules`
**Chức năng**: Lịch học định kỳ cho learning paths

**Tạo thói quen học**:
- Học Java mỗi Thứ 2, 4, 6 lúc 19:30
- Mỗi session 60 phút
- Nhắc nhở trước 30 phút

**Tracking**:
- `completed_sessions`: Số buổi đã học
- `missed_sessions`: Số buổi bỏ lỡ
- `last_studied_at`: Học lần cuối

**Use cases**:
- Tạo habit học tập đều đặn
- Reminder notifications
- Analyze learning consistency

---

## 12. Chat Conversations

### 🎯 Mục đích chính
Hệ thống chat với AI assistant (GPT-like).

### 📋 Các bảng

#### `chat_conversations`
**Chức năng**: Thread chat của user

**Đặc điểm**:
- Mỗi conversation có title (auto-generated hoặc user đặt)
- Có thể archive hoặc delete
- Track số message và thời gian message cuối

**Use cases**:
- User có thể chat nhiều topics khác nhau
- Sidebar hiển thị list conversations
- Search conversations

#### `chat_messages`
**Chức năng**: Các tin nhắn trong conversation

**Các role**:
- `user`: Tin nhắn từ user
- `assistant`: Response từ AI
- `system`: System messages

**Tracking**:
- `token_count`: Số token sử dụng (cho billing)
- `metadata`: Thông tin thêm (model used, temperature, etc.)

**Workflow**:
1. User gửi message "Hướng dẫn tôi học Laravel"
2. Tạo record với role = 'user'
3. Call AI API
4. Tạo record với role = 'assistant' + response
5. Update conversation.last_message_at

**Use cases**:
- AI coaching
- Giải đáp thắc mắc
- Brainstorming ideas
- Code review

---

## 13. Cheat Code / Code Learning Platform

### 🎯 Mục đích chính
Platform học lập trình với code examples và exercises (giống LeetCode + W3Schools).

### 📋 Các bảng

#### `cheat_code_languages`
**Chức năng**: Danh sách ngôn ngữ lập trình

**Thông tin**:
- `name`: Tên kỹ thuật (php, java, python)
- `display_name`: Tên hiển thị (PHP, Java, Python)
- `slug`: URL slug
- `icon` & `color`: Branding
- `category`: programming, markup, database
- `popularity`: Độ phổ biến (0-100)

**Counters**:
- `sections_count`: Số sections
- `examples_count`: Số code examples
- `exercises_count`: Số bài tập

**Ví dụ languages**:
- PHP, Java, Python, JavaScript, Go, Kotlin, C++
- HTML, CSS, YAML (markup)
- MySQL, PostgreSQL (database)

#### `cheat_code_sections`
**Chức năng**: Phân chia nội dung học theo sections

**Ví dụ cho PHP**:
- Getting Started
- Variables & Data Types
- Control Structures
- Functions
- OOP
- Database
- Laravel Framework

**Mỗi section**:
- Có nhiều code examples
- Có sort_order
- Có thể publish/unpublish

#### `code_examples`
**Chức năng**: Code examples cụ thể

**Ví dụ**: Section "Variables" trong PHP
- Example 1: hello.php - Print Hello World
- Example 2: variables.php - Variable declaration
- Example 3: string-concat.php - String concatenation

**Thông tin example**:
- `code`: Source code
- `description`: Giải thích
- `output`: Kết quả khi chạy
- `difficulty`: easy, medium, hard
- `tags`: JSON array

**Engagement**:
- `views_count`: Số lượt xem
- `favorites_count`: Số lượt favorite

#### `exercises`
**Chức năng**: Bài tập lập trình

**Cấu trúc bài tập**:
- `title` & `description`: Mô tả bài toán
- `question`: Yêu cầu cụ thể
- `starter_code`: Code template
- `solution`: Lời giải (ẩn)
- `hints`: Gợi ý (JSON array)
- `difficulty`: easy, medium, hard
- `points`: Điểm thưởng
- `time_limit`: Giới hạn thời gian (phút)

**Ví dụ exercise**:
```
Title: "FizzBuzz Problem"
Difficulty: Easy
Points: 10
Question: "Write a function that prints numbers 1-100,
but for multiples of 3 print 'Fizz',
for multiples of 5 print 'Buzz',
for multiples of both print 'FizzBuzz'"
```

**Statistics**:
- `submissions_count`: Số lần nộp bài
- `success_count`: Số lần AC (Accepted)
- `success_rate`: Tỷ lệ AC

#### `exercise_test_cases`
**Chức năng**: Test cases để chấm bài

**Ví dụ cho FizzBuzz**:
- Test 1: input = 15 → output = "FizzBuzz"
- Test 2: input = 9 → output = "Fizz"
- Test 3: input = 10 → output = "Buzz"
- Test 4: input = 7 → output = "7"

**Loại test cases**:
- `is_sample`: Test cases hiển thị cho user xem
- `is_hidden`: Hidden test cases (user không thấy)

#### `user_exercise_submissions`
**Chức năng**: Lịch sử nộp bài của user

**Workflow nộp bài**:
1. User viết code và submit
2. Tạo record với status = 'pending'
3. Hệ thống chạy code với test cases
4. Update status:
   - `success`: Pass all tests
   - `failed`: Failed some tests
   - `error`: Compilation error
   - `timeout`: Quá thời gian

**Thông tin chi tiết**:
- `code`: Code user submit
- `passed_test_cases` / `total_test_cases`
- `score`: Điểm đạt được
- `execution_time`: Thời gian chạy (ms)
- `memory_used`: Bộ nhớ dùng (KB)
- `error_message`: Lỗi nếu có
- `test_results`: Chi tiết từng test case (JSON)

#### `user_code_favorites`
**Chức năng**: User bookmark code examples yêu thích

**Use cases**:
- Quick reference
- Personal code library
- Share với bạn bè

#### `user_exercise_progress`
**Chức năng**: Track tiến độ làm bài tập

**Thông tin**:
- `is_completed`: Đã hoàn thành chưa
- `best_score`: Điểm cao nhất
- `attempts_count`: Số lần thử
- `last_attempted_at`: Lần thử cuối

**Gamification**:
- Badge: Hoàn thành 10 bài easy
- Leaderboard: Top users theo points
- Streaks: Làm bài mỗi ngày

---

## 14. Cache Tables

### 🎯 Mục đích chính
Laravel cache system để tối ưu performance.

### 📋 Các bảng

#### `cache`
**Chức năng**: Lưu trữ cache data

**Cách hoạt động**:
- `key`: Cache key (unique)
- `value`: Dữ liệu cached (serialized)
- `expiration`: Timestamp hết hạn

**Ví dụ cache**:
- User profile data
- API responses
- Computed statistics
- Session data

#### `cache_locks`
**Chức năng**: Distributed locking

**Use cases**:
- Prevent race conditions
- Ensure only one process runs cron jobs
- Queue processing locks

---

## 📊 Tổng kết Mối quan hệ giữa các nhóm

### Luồng dữ liệu chính:

```
USER
 ├─> USER_SETTINGS (cài đặt)
 ├─> LEARNING_PATHS (lộ trình học)
 │    └─> LEARNING_MILESTONES
 │         └─> TASKS (gắn với milestone)
 ├─> PROJECTS
 │    └─> TASKS (gắn với project)
 ├─> TASKS (standalone)
 │    ├─> SUBTASKS
 │    ├─> FOCUS_SESSIONS
 │    │    ├─> DISTRACTION_LOGS
 │    │    └─> FOCUS_ENVIRONMENTS
 │    ├─> AI_SUGGESTIONS (breakdown task)
 │    └─> KNOWLEDGE_ITEMS (tạo notes từ task)
 ├─> DAILY_CHECKINS (buổi sáng)
 ├─> DAILY_REVIEWS (buổi tối)
 ├─> KNOWLEDGE_CATEGORIES
 │    └─> KNOWLEDGE_ITEMS (spaced repetition)
 ├─> TIMETABLE_CLASSES
 │    ├─> TIMETABLE_STUDIES (homework)
 │    └─> WEEKLY_CONTENTS
 ├─> CHAT_CONVERSATIONS
 │    └─> CHAT_MESSAGES (AI coaching)
 └─> CODE_LEARNING
      ├─> CODE_FAVORITES
      ├─> EXERCISE_SUBMISSIONS
      └─> EXERCISE_PROGRESS
```

### Use Case End-to-End:

**Scenario**: User muốn học Java Full Stack

1. **Tạo Learning Path** (từ template hoặc AI generate)
   - Table: `learning_paths`
   - AI tạo roadmap với milestones

2. **Milestones được tạo**:
   - Table: `learning_milestones`
   - Java Basics → OOP → Spring Boot → Database → Projects

3. **Tasks được tạo cho milestone đầu**:
   - Table: `tasks`
   - "Học variables", "Học loops", "Làm bài tập"

4. **User làm task "Học variables"**:
   - Table: `focus_sessions` - Bắt đầu Pomodoro
   - Table: `focus_environments` - Checklist môi trường
   - Table: `distraction_logs` - Nếu bị distract

5. **Sau khi học xong**:
   - Table: `knowledge_items` - Tạo notes về variables
   - Tag: #java #basics #variables

6. **Làm bài tập code**:
   - Table: `exercises` - Chọn bài "Java Variables Exercise"
   - Table: `user_exercise_submissions` - Submit code
   - Table: `user_exercise_progress` - Update progress

7. **Cuối ngày**:
   - Table: `daily_reviews` - Reflection
   - Table: `user_stats` - Update statistics
   - Table: `ai_summaries` - AI tạo summary

8. **Hệ thống AI phân tích**:
   - Dựa vào `focus_sessions`, `distraction_logs`, `daily_reviews`
   - Table: `ai_suggestions` - Đề xuất cải thiện

9. **Spaced Repetition**:
   - Table: `knowledge_items` - Review lại notes về variables sau 7 ngày
   - Update `next_review_date`

---

## 🎯 Các tính năng nổi bật của hệ thống

### 1. **Deep Work Optimization**
- Energy-based task scheduling
- Context switch warnings
- Focus environment checklist
- Distraction tracking

### 2. **AI-Powered Learning**
- Auto-generate learning roadmaps
- Smart task breakdown
- Personalized suggestions
- Daily/weekly summaries

### 3. **Spaced Repetition**
- Knowledge items với review scheduling
- Retention score tracking
- Optimal review intervals

### 4. **Comprehensive Analytics**
- Daily stats và trends
- Performance metrics
- Activity logs
- Correlation analysis (sleep vs productivity)

### 5. **Code Learning Platform**
- Code examples library
- Interactive exercises
- Auto-grading với test cases
- Progress tracking

### 6. **Holistic Productivity**
- Morning check-ins
- Evening reflections
- Mood & energy tracking
- Work-life balance scoring

---

## 🔄 Migration & Seeding Strategy

### Migration Order:
1. Core tables (users, auth)
2. Settings & profiles
3. Learning paths & templates
4. Projects & tasks
5. Focus & productivity
6. Analytics & AI
7. Knowledge management
8. Timetable
9. Chat
10. Code learning platform
11. Cache

### Seeding:
- **UserSeeder**: Demo users
- **Language Seeders**: Tạo data cho 10+ ngôn ngữ lập trình
- **Course Seeders**: Pre-built learning paths
- **CheatCode Seeders**: Code examples và exercises

---

## 📚 Database Best Practices được áp dụng

### 1. **Indexes**
- Composite indexes cho queries phổ biến
- Foreign key indexes
- Timestamp indexes

### 2. **Data Types**
- Sử dụng ENUM cho fixed values
- JSON cho flexible data
- Decimal cho percentages
- Appropriate varchar lengths

### 3. **Relationships**
- Cascading deletes khi cần
- Nullable foreign keys
- Many-to-many pivot tables

### 4. **Audit Fields**
- `created_at`, `updated_at` trên mọi table
- Soft deletes support
- Activity logging

### 5. **Performance**
- Cache tables cho hot data
- Counters (denormalization) cho sections_count, etc.
- Pagination-ready indexes

---

**Tác giả**: ToDoApp Development Team
**Ngày cập nhật**: 2025-01-20
**Database Version**: 1.0
**Laravel Version**: 11.x
