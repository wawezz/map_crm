<?php

namespace backend\common\mailer;

class Mailer extends \yii\swiftmailer\Mailer
{
    /**
     * Renders the specified view with optional parameters and layout.
     * The view will be rendered using the [[view]] component.
     * @param string $view the view name or the [path alias](guide:concept-aliases) of the view file.
     * @param array $params the parameters (name-value pairs) that will be extracted and made available in the view file.
     * @param string|bool $layout layout view name or [path alias](guide:concept-aliases). If false, no layout will be applied.
     * @return string the rendering result.
     */
    public function render($view, $params = [], $layout = false)
    {
        $output = $this->getView()->render($view, $params, $this);
        if ($layout !== false) {
            return $this->getView()->render($layout, [
                'content' => $output,
                'message' => $params['message'],
                'subtitle' => $params['subtitle'] ?? '',
            ], $this);
        }

        return $output;
    }
}
