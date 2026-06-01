<?php

return [
    'required' => ':attributeは必須項目です。',
    'email' => '有効なメールアドレスの形式で入力してください。',
    'max' => [
        'string' => ':attributeは:max文字以内で入力してください。',
    ],
    'min' => [
        'string' => ':attributeは:min文字以上で入力してください。',
    ],
    'unique' => 'この:attributeは既に登録されています。',
    'confirmed' => 'パスワードが確認用と一致していません。',

    // 各項目（属性）の日本語名定義
    'attributes' => [
        'name' => '名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
    ],
];