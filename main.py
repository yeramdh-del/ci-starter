import random
from datetime import datetime
from pathlib import Path

def pad_id(num):
    return f"{num:010d}"

def generate_sql_random_tree(num_root_comments=100, total_comments=10000, max_depth=6, output_file='insert_comments.sql'):
    sql_lines = []

    #mysql 문자셋
    sql_lines.append('SET NAMES utf8mb4;')
    sql_lines.append('')
    comment_id = 1
    tree_id = 1
    created_at = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

    # 구조 추적용: comment_id -> dict(depth, parent, path, root)
    comments = {}

    board_idx = 1
    user_idx = 1

    # 최상위 댓글 먼저 생성
    for i in range(num_root_comments):
        content = f'댓글 {i+1}'
        sql_lines.append(
            f"INSERT INTO v2_comments (idx, board_idx, user_idx, content, created_at, updated_at, parent_idx) "
            f"VALUES ({comment_id}, {board_idx}, {user_idx}, '{content}', '{created_at}', '{created_at}', NULL);"
        )
        path = f"{pad_id(comment_id)}/"
        sql_lines.append(
            f"INSERT INTO v2_comment_tree (idx, comment_idx, depth, path, root_idx) "
            f"VALUES ({tree_id}, {comment_id}, 0, '{path}', {comment_id});"
        )

        comments[comment_id] = {
            'depth': 0,
            'parent': None,
            'path': path,
            'root': comment_id
        }

        comment_id += 1
        tree_id += 1

    remaining_comments = total_comments - num_root_comments

    valid_parents = list(comments.keys())  # 가능한 부모 목록

    for i in range(remaining_comments):
        # 부모 댓글 랜덤 선택 (깊이 제한)
        while True:
            parent_id = random.choice(valid_parents)
            parent_info = comments[parent_id]
            if parent_info['depth'] < max_depth - 1:
                break

        new_depth = parent_info['depth'] + 1
        new_path = parent_info['path'] + f"{pad_id(comment_id)}/"
        root_idx = parent_info['root']

        content = f'댓글 랜덤 {i+1}'

        sql_lines.append(
            f"INSERT INTO v2_comments (idx, board_idx, user_idx, content, created_at, updated_at, parent_idx) "
            f"VALUES ({comment_id}, {board_idx}, {user_idx}, '{content}', '{created_at}', '{created_at}', {parent_id});"
        )
        sql_lines.append(
            f"INSERT INTO v2_comment_tree (idx, comment_idx, depth, path, root_idx) "
            f"VALUES ({tree_id}, {comment_id}, {new_depth}, '{new_path}', {root_idx});"
        )

        # 현재 댓글 정보 저장
        comments[comment_id] = {
            'depth': new_depth,
            'parent': parent_id,
            'path': new_path,
            'root': root_idx
        }

        # 추가 댓글도 다음 댓글들의 부모가 될 수 있음
        valid_parents.append(comment_id)

        comment_id += 1
        tree_id += 1

    Path(output_file).write_text('\n'.join(sql_lines), encoding='utf-8')
    print(f"SQL file '{output_file}' generated with {comment_id - 1} comments.")

# 실행 예시
generate_sql_random_tree()