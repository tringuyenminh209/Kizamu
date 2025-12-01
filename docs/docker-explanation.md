# Giải Thích Về Docker, Dockerfile và Docker Compose

## Khái Niệm Cơ Bản

### 1. Dockerfile là gì?

**Dockerfile** là công thức để tạo một **Docker Image** (giống như một bản thiết kế).

Ví dụ trong Dockerfile của bạn:
```dockerfile
FROM php:8.4-fpm          # Bắt đầu từ image PHP có sẵn
RUN apt-get install ...    # Cài đặt các package
RUN docker-php-ext-install # Cài PHP extensions
CMD ["supervisord"]        # Lệnh chạy khi container khởi động
```

**Khi build:** Docker đọc Dockerfile → Tạo Image (như một file .iso)

### 2. docker-compose.yml là gì?

**docker-compose.yml** là file cấu hình để chạy nhiều containers cùng lúc và kết nối chúng với nhau.

Trong file của bạn có 3 services:
- `app`: Laravel application
- `mysql`: Database
- `redis`: Cache

---

## Flow Khi Chạy `docker-compose up -d`

Khi bạn chạy `docker-compose up -d`, đây là những gì xảy ra:

### Bước 1: Đọc docker-compose.yml
```
Docker Compose đọc file → Phát hiện 3 services: app, mysql, redis
```

### Bước 2: Tạo Network
```
Tạo network "kizamu-network" 
→ Tất cả containers sẽ nói chuyện với nhau qua network này
```

### Bước 3: Tạo Volumes
```
Tạo volumes: mysql_data, redis_data
→ Lưu trữ dữ liệu database và cache (dữ liệu không mất khi container restart)
```

### Bước 4: Build Image cho service "app"
```
Kiểm tra: Image đã tồn tại chưa?
├─ Nếu CHƯA → Chạy Dockerfile để build image
│   ├─ FROM php:8.4-fpm (tải image PHP nếu chưa có)
│   ├─ RUN apt-get install ... (cài packages)
│   ├─ RUN cài Node.js
│   ├─ RUN cài PHP extensions
│   └─ COPY php.ini (copy config)
│
└─ Nếu ĐÃ CÓ → Kiểm tra có thay đổi không?
    ├─ Có thay đổi → Rebuild
    └─ Không thay đổi → Dùng image cũ
```

### Bước 5: Pull Images cho mysql và redis
```
mysql: Tải image mysql:8.0 từ Docker Hub (nếu chưa có)
redis: Tải image redis:7-alpine từ Docker Hub (nếu chưa có)
```

### Bước 6: Khởi động containers theo thứ tự depends_on
```
1. Khởi động mysql container TRƯỚC
   └─ Container: kizamu-mysql
   └─ Port: 9002:3306 (máy bạn:9002 → container:3306)
   └─ Volume: mysql_data (lưu database files)

2. Khởi động redis container
   └─ Container: kizamu-redis  
   └─ Port: 63790:6379
   └─ Volume: redis_data

3. Khởi động app container SAU (vì depends_on mysql, redis)
   └─ Container: kizamu-backend
   └─ Build từ Dockerfile
   └─ Mount volumes:
       ├─ ./backend → /var/www (code của bạn)
       ├─ ./docker/nginx.conf → /etc/nginx/... (config nginx)
       └─ ./docker/supervisord.conf → /etc/supervisor/... (config supervisor)
   └─ Port: 9001:80 (máy bạn:9001 → container:80)
   └─ Environment variables từ .env
   └─ Chạy CMD: supervisord (khởi động nginx + php-fpm)
```

### Bước 7: Kết nối containers
```
Tất cả containers join vào network "kizamu-network"
→ app có thể gọi mysql bằng hostname "mysql"
→ app có thể gọi redis bằng hostname "redis"
```

---

## Minh Họa Bằng Sơ Đồ

```
┌─────────────────────────────────────────────────┐
│  Máy tính của bạn (Host)                        │
│                                                  │
│  ┌──────────────────────────────────────────┐  │
│  │ Docker Engine                              │  │
│  │                                            │  │
│  │  ┌────────────────────────────────────┐   │  │
│  │  │ Network: kizamu-network             │   │  │
│  │  │                                     │   │  │
│  │  │  ┌──────────────┐  ┌─────────────┐ │   │  │
│  │  │  │ mysql        │  │ redis       │ │   │  │
│  │  │  │ Container    │  │ Container   │ │   │  │
│  │  │  │ Port: 3306   │  │ Port: 6379  │ │   │  │
│  │  │  └──────┬───────┘  └──────┬──────┘ │   │  │
│  │  │         │                  │        │   │  │
│  │  │         └────────┬─────────┘        │   │  │
│  │  │                  │                  │   │  │
│  │  │         ┌────────▼─────────┐        │   │  │
│  │  │         │ app Container    │        │   │  │
│  │  │         │ (Laravel)        │        │   │  │
│  │  │         │ Port: 80         │        │   │  │
│  │  │         │                  │        │   │  │
│  │  │         │ Volume mounts:   │        │   │  │
│  │  │         │ ./backend ←→ /var/www    │   │  │
│  │  │         └──────────────────┘        │   │  │
│  │  └────────────────────────────────────┘   │  │
│  │                                            │  │
│  │  Volumes:                                  │  │
│  │  ┌─────────────┐  ┌─────────────┐         │  │
│  │  │ mysql_data  │  │ redis_data  │         │  │
│  │  └─────────────┘  └─────────────┘         │  │
│  └────────────────────────────────────────────┘  │
│                                                  │
│  Port mappings:                                  │
│  localhost:9001 → app:80                         │
│  localhost:9002 → mysql:3306                     │
│  localhost:63790 → redis:6379                    │
└──────────────────────────────────────────────────┘
```

---

## So Sánh Dockerfile vs docker-compose.yml

| | Dockerfile | docker-compose.yml |
|---|---|---|
| **Mục đích** | Tạo Image (môi trường) | Chạy nhiều Containers |
| **Khi nào dùng** | Build image | Quản lý ứng dụng |
| **Lệnh** | `docker build` | `docker-compose up` |
| **Ví dụ** | Cài PHP, Node.js | Chạy app + mysql + redis |

---

## Ví Dụ Thực Tế

Khi bạn vào container và chạy:
```bash
docker-compose exec app bash
cd /var/www
npm install
```

**Điều gì xảy ra:**

1. `docker-compose exec app` → Vào container `kizamu-backend`
2. Container này được build từ Dockerfile → có Node.js và npm
3. `/var/www` là volume mount từ `./backend` trên máy bạn
4. Khi bạn chạy `npm install` → node_modules được tạo trong `/var/www/node_modules`
5. Vì volume mount, `node_modules` cũng xuất hiện trong `./backend/node_modules` trên máy bạn

---

## Tóm Tắt Flow

```
1. docker-compose up -d
   ↓
2. Đọc docker-compose.yml
   ↓
3. Tạo network + volumes
   ↓
4. Build/pull images
   ├─ app: build từ Dockerfile
   ├─ mysql: pull mysql:8.0
   └─ redis: pull redis:7-alpine
   ↓
5. Tạo containers
   ├─ mysql (chạy trước)
   ├─ redis (chạy trước)
   └─ app (chạy sau, vì depends_on)
   ↓
6. Kết nối containers qua network
   ↓
7. Mount volumes (code, config)
   ↓
8. Chạy CMD trong mỗi container
   ├─ mysql: mysqld
   ├─ redis: redis-server
   └─ app: supervisord → nginx + php-fpm
   ↓
9. ✅ Tất cả đã chạy!
```

---

## Các Khái Niệm Quan Trọng

### Image vs Container

- **Image**: Bản template, không thay đổi (như file .iso)
- **Container**: Instance chạy từ Image (như chạy máy ảo từ file .iso)

### Volume Mount

Volume mount cho phép chia sẻ thư mục giữa máy host và container:

```yaml
volumes:
  - ./backend:/var/www
```

- `./backend`: Thư mục trên máy bạn
- `/var/www`: Thư mục trong container
- Thay đổi ở một bên → tự động sync sang bên kia

### Port Mapping

```yaml
ports:
  - "9001:80"
```

- `9001`: Port trên máy bạn
- `80`: Port trong container
- Truy cập `localhost:9001` → kết nối đến container port 80

### Network

Containers trong cùng network có thể giao tiếp với nhau bằng hostname:

- App container gọi MySQL: `mysql:3306` (không cần IP)
- App container gọi Redis: `redis:6379`

### depends_on

Đảm bảo thứ tự khởi động:
- `app` depends_on `mysql` và `redis`
- → MySQL và Redis khởi động trước
- → App khởi động sau

---

## Các Lệnh Docker Thường Dùng

### Quản lý containers
```bash
# Khởi động tất cả services
docker-compose up -d

# Dừng tất cả services
docker-compose down

# Xem logs
docker-compose logs -f

# Rebuild image
docker-compose build app

# Restart service
docker-compose restart app
```

### Vào container
```bash
# Vào container app
docker-compose exec app bash

# Chạy lệnh trong container
docker-compose exec app php artisan migrate
```

### Xem thông tin
```bash
# Xem containers đang chạy
docker-compose ps

# Xem images
docker images

# Xem networks
docker network ls

# Xem volumes
docker volume ls
```

---

## Troubleshooting

### Container không khởi động
```bash
# Xem logs
docker-compose logs app

# Kiểm tra status
docker-compose ps
```

### Rebuild lại từ đầu
```bash
# Xóa containers và volumes
docker-compose down -v

# Build lại
docker-compose build --no-cache

# Khởi động lại
docker-compose up -d
```

### Vào container để debug
```bash
docker-compose exec app bash
# Bây giờ bạn đang ở trong container
# Có thể chạy các lệnh như: php, composer, npm, etc.
```

---

## Kết Luận

- **Dockerfile**: Công thức tạo môi trường (Image)
- **docker-compose.yml**: Cấu hình để chạy nhiều services cùng lúc
- **Volume**: Chia sẻ file giữa host và container
- **Network**: Kết nối các containers với nhau
- **Port mapping**: Expose port từ container ra ngoài

Docker giúp đảm bảo môi trường development giống nhau trên mọi máy, không cần cài đặt PHP, MySQL, Redis trực tiếp trên máy.

