#include <net_xp_framework_turpitude_PHPCompiledScript.h>
#include <Turpitude.h>


JNIEXPORT jobject JNICALL Java_net_xp_1framework_turpitude_PHPCompiledScript_execute(JNIEnv* env, jobject self, jobject ctx) {
    // find class
    jclass myclass = env->GetObjectClass(self);
    if (NULL == myclass)
        java_throw(env, "javax/script/ScriptException", "unable to find class via GetObjectClass");
    // find ZendOpArrayptr field if
    jfieldID oparrayField = env->GetFieldID(myclass, "ZendOpArrayptr", "Ljava/nio/ByteBuffer;");
    if (NULL == oparrayField) 
        java_throw(env, "javax/script/ScriptException", "unable find fieldID (ZendOpArrayptr)");
    // retrieve pointer to op_array
    zend_op_array* compiled_op_array = NULL;
    compiled_op_array = (zend_op_array*)(
        env->GetDirectBufferAddress(env->GetObjectField(self, oparrayField))
    );
    if (NULL == compiled_op_array)
        java_throw(env, "javax/script/ScriptException", "ZendOpArrayptr empty");

    // execute!
    zend_first_try {
        zend_llist global_vars;
        zend_llist_init(&global_vars, sizeof(char *), NULL, 0);
        
        zval *retval_ptr = NULL;
        zend_fcall_info_cache fci_cache;
        zend_fcall_info fci;
         
        memset(&fci, 0, sizeof(fci));
        memset(&fci_cache, 0, sizeof(fci_cache));
         
        fci.size = sizeof(fci);
        fci.function_table = CG(function_table);

        fci.retval_ptr_ptr = &retval_ptr;
        fci.no_separation = 1;

        fci_cache.initialized = 1;
        fci_cache.function_handler = (zend_function*)compiled_op_array;
        compiled_op_array->type = ZEND_USER_FUNCTION;

        zend_call_function(&fci, &fci_cache TSRMLS_CC);
       
        zend_llist_destroy(&global_vars);
    } zend_catch {
        java_throw(env, "java/lang/IllegalArgumentException", "Bailout");
    } zend_end_try();

    jclass retcls = env->FindClass("java/lang/Boolean");
    jmethodID retmid = env->GetMethodID(retcls, "<init>", "(Z)V");
    return env->NewObject(retcls, retmid, true);
}


