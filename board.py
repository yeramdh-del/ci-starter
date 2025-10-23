import random
from datetime import datetime

# 랜덤 사용자 및 카테고리 설정
user_ids = [1, 5, 7, 9, 10, 14, 15]
category_ids = [22, 23, 24]

# SQL 파일 이름
output_file = "insert_board.sql"

# SQL 파일 생성
with open(output_file, "w", encoding="utf-8") as f:
    f.write("-- Auto-generated SQL insert script for board table\n")
    f.write("-- Generated at {}\n\n".format(datetime.now().strftime("%Y-%m-%d %H:%M:%S")))
    
    for i in range(1, 101):
        title = f"테스트 게시글 {i}"
        content = (
            f"이것은 테스트 게시글 {i}의 내용입니다. "
            f"랜덤한 텍스트를 포함하고 있습니다. 😊\n"
            f"이 게시글은 카테고리 {random.choice(category_ids)} 에 속합니다."
        )
        user_idx = random.choice(user_ids)
        category_idx = random.choice(category_ids)
        
        sql = (
            f"INSERT INTO `board` "
            f"(`title`, `content`, `created_at`, `updated_at`, `user_idx`, `category_idx`) "
            f"VALUES ('{title}', '{content}', NOW(), NOW(), {user_idx}, {category_idx});\n"
        )
        f.write(sql)

print(f"✅ SQL 파일 생성 완료: {output_file}")